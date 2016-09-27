<?php

namespace App\Http\Controllers;

use App\City;
use App\Helpers\Helper;
use App\Http\Requests\SearchLocation;
use App\Keyword;
use App\LogEmail;
use App\Playlist;
use App\PlaylistKey;
use App\PlaylistVideo;
use App\VideoCache;
use Auth;
use GetYourGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Image;
use Session;
use Youtube;

class SearchController extends Controller
{
    //
    protected $vcRepo;

    public function __construct(VideoCache $vcRepo)
    {
        parent::__construct();

        $this->vcRepo = $vcRepo;
    }

    public function index()
    {
        $page_title = trans('meta_data.search_funnel_title');

        $page_desc = trans('meta_data.search_funnel_desc');

        $page_img = asset('img/playligo_home_background_glacier.jpg');

        return view('search.search', compact('page_title', 'page_desc', 'page_img'));
    }

    public function searchKeywords(SearchLocation $request)
    {
        session()->put('search_location', ucwords($request->input('location')));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['redirect' => url('new_search_keywords')]);
        } else {
            return redirect('new_search_keywords');
        }
    }

    public function displaySearchKeywords(Request $request)
    {
        if (!($location = session()->get('search_location'))) {
            return redirect(url('search'));
        }

        $page_title = trans('meta_data.search_funnel_title') . ' | ' . $location;

        $page_desc = trans('meta_data.search_funnel_desc');

        $page_img = asset('img/playligo_home_background_glacier.jpg');

        $general_keywords = Keyword::orderBy('weight', 'desc')->get();

        $split = false;
        $split = false;

        $default = [
            'aerial view',
            'food'
        ];

        return view('search.search_keywords', compact(
            'location',
            'page_title',
            'page_desc',
            'page_img',
            'default',
            'general_keywords',
            'places_of_interest',
            'split'
        ));
    }

    /**
     * Return Places of Interests from Google Maps
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPlacesOfInterests()
    {
        $coordinates = session()->get('location_coordinates');
        $location = session()->get('search_location');
        $current_keyword = 0;
        $max_keywords = Keyword::MAX_KEYWORDS;
        $keywords = [];
        $default = [];
        do {
            $next_page_token = null;
            $json_results = json_decode(GoogleMaps::load('textsearch')
                ->setParam([
                    'query'     => 'point_of_interest ' . $location,
                    'language'  => 'en',
                    'location'  => implode(',', $coordinates),
                    'type'      => 'point_of_interest',
                    'pagetoken' => $next_page_token
                ])
                ->get()
            );
            foreach ($json_results->results as $keyword) {
                array_push($keywords, [
                    'name'        => $keyword->name,
                    'image_path'  => Keyword::getGooglePlacePhotoUrl($keyword),
                    'description' => $keyword->formatted_address,
                ]);
                if (++$current_keyword == $max_keywords) {
                    break;
                }
            }
            $next_page_token = isset($json_results->next_page_token) ? $json_results->next_page_token : null;
            if (empty($next_page_token) || $current_keyword == $max_keywords) {
                break;
            }
        } while ($current_keyword <= $max_keywords);

        return view('search.partials._keywords', compact(
            'keywords',
            'default'
        ));
    }

    /**
     * Retrieve tours from getyourguide api
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTours(Request $request)
    {
        $coordinates = [
            'lat' => $request->input('lat'),
            'lng' => $request->input('lng'),
        ];
        $max_keywords = Keyword::MAX_KEYWORDS;
        $keywords = GetYourGuide::getTours([
            'coordinates[lat]' => $coordinates['lat'],
            'coordinates[lng]' => $coordinates['lng'],
            'limit'            => $max_keywords
        ])['results'];
        $default = [];
        $split = true;

        return view('search.partials._keywords', compact(
            'keywords',
            'default',
            'split'
        ));
    }

    public function autoGen(Request $request)
    {
        $input = $request->input();

        $keys = explode(",", array_get($input, 'search_keys'));

        $location = array_get($input, 'location');

        $resultsets = $this->fetchVideos($location, $keys, false, 0.25, true, $keys_used);
        session()->put('search_keys', $keys_used);
        session()->put('video_meta', $resultsets);

        $this->autoGenPlaylist($resultsets);

        $redirect = route('playlist.demo');

        if ($user = Auth::user()) {
            $pl_slug = '';
            do {
                $pl_slug = Helper::generateRandomString();
            } while (Playlist::wherePlSlug($pl_slug)->count() != 0);
            // Create playlist
            $playlist = Playlist::create(['pl_user' => Auth::user()->id, 'pl_title' => '', 'pl_location' => $location, 'pl_slug' => $pl_slug]);

            // Create playlist videos
            $plv = new PlaylistVideo;
            $plv->massCreate($playlist->pl_id, session()->get('selected', []), session()->get('search_keys'));

            foreach ($keys_used as $key_used) {
                PlaylistKey::create(['plk_playlist' => $playlist->pl_id, 'plk_key' => $key_used['value'], 'plk_weight' => $key_used['weight'], 'plk_next_token' => $key_used['next_token']]);
            }

            // Email notification
            $email = new LogEmail;

            $email->sendNewPlaylist($playlist);
            $redirect = url('playlist/edit/' . $playlist->pl_id);
        } else {
            session()->put('create_from_demo', true);
        }

        return response()->json(['redirect' => $redirect, 'message' => trans('messages.autogen_successful')]);
    }

    public function fetchVideos($location, $keys, $more = false, $result_multiplier = 1, $use_default = true, &$keys_used = [])
    {
        $result = [];

        $keys = array_filter($keys);

        // User keywords
        foreach ($keys as $key) {
            if (!empty($key)) {
                $key_result = $this->fetchVideosByKeyword($location, $key, $result_multiplier * config('youtube.user_key_weight'), $more);

                $result[$key] = $key_result['results'];

                $keys_used[] = ['value' => $key, 'weight' => 1, 'next_token' => $key_result['info']['nextPageToken']];
            }
        }

        $this->vcRepo->massCreate($result);

        return $result;
    }

    protected function fetchVideosByKeyword($location, $key, $max_result, $more)
    {
        if (!empty($key)) {
            $params = [
                'q'             => $location . ' ' . $key,
                'type'          => 'video',
                'part'          => 'id, snippet',
                'videoDuration' => 'short',
                'safeSearch'    => 'strict',
                // 'order' => 'rating',
                'maxResults'    => $max_result
            ];

            if ($more) {
                $params['pageToken'] = session()->get('search_keywords.' . $key);
                // $info = session()->get('search_keywords.' . $key);
                // $params['pageToken'] = $info['nextPageToken'];
            }

            $key_result = Youtube::searchAdvanced($params, true);

            // session()->put('search_keywords.' . $key, $key_result['info']);
            session()->put('search_keywords.' . $key, $key_result['info']['nextPageToken']);

            // return array_values($key_result['results']);
            // return array_values($key_result);
            return $key_result;
        }
    }

    private function autoGenPlaylist($resultsets)
    {
        $playlist = [];
        session()->forget('selected');
        foreach ($resultsets as $set) {
            if (!empty($set)) {
                foreach ($set as $video) {
                    $playlist[] = $video;
                    session()->push('selected', $video->id->videoId);
                }
            }
        }
        return $playlist;
    }

    /**
     * Get video ids by specified keywords.
     * Needed for filtering videos by keywords
     *
     * @param  Request $request passed request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByKeyword(Request $request)
    {
        $videos = Youtube::searchAdvanced(
            [
                'q'          => $request->input('query'), 'part' => 'id',
                'maxResults' => 30
            ],
            true
        );
        Playlist::find($request->input('pl_id'))->increment('pl_keyword_clicks');
        return response()->json(collect($videos)->flatten()->pluck('id')->pluck('videoId'));
    }

    /**
     * Show demo page for playlist
     */
    public function editDemoPlaylist()
    {
        if (!session()->has('selected') ||
            !session()->has('search_keys') ||
            !session()->has('search_location') ||
            !session()->has('video_meta')
        ) {
            if (Auth::user()) {
                return redirect('playlist');
            } else {
                return redirect('login');
            }
        }
        $selected = session()->get('selected');
        $pl_keys = session()->get('search_keys');
        $location = session()->get('search_location');
        $video_meta = session()->get('video_meta');

        if (($user = Auth::user()) && (session()->has('create_from_demo'))) {
            $default_playlist_title = '';
            $pl_slug = '';
            do {
                $pl_slug = Helper::generateRandomString();
            } while (Playlist::wherePlSlug($pl_slug)->count() != 0);
            // Create playlist
            $playlist = Playlist::create(['pl_user' => Auth::user()->id, 'pl_title' => $default_playlist_title, 'pl_location' => $location, 'pl_slug' => $pl_slug]);

            // Create playlist videos
            $plv = new PlaylistVideo;

            $plv->massCreate($playlist->pl_id, session()->get('selected', []), session()->get('search_keys'));

            // Create playlist keys
            $plk = new PlaylistKey;

            foreach ($pl_keys as $key_used) {
                $plk->create(['plk_playlist' => $playlist->pl_id, 'plk_key' => $key_used['value'], 'plk_weight' => $key_used['weight'], 'plk_next_token' => $key_used['next_token']]);
            }

            // Email notification
            $email = new LogEmail;

            $email->sendNewPlaylist($playlist);
            $redirect = url('playlist/edit/' . $playlist->pl_id);
            session()->forget('create_from_demo');
            session()->forget('search_keys');
            session()->forget('video_meta');

            return redirect($redirect);
        }

        $videos = new Collection;
        foreach ($video_meta as $key => $group) {
            if (!empty($group)) {
                foreach ($group as $item) {
                    $videos->push([
                        'video_id' => $item->id->videoId,
                        'kind'     => $item->kind,
                        'etag'     => $item->etag,
                        'snippet'  => serialize($item->snippet)
                    ]);
                }
            }
        }

        $keys = [];
        foreach ($pl_keys as $pl_key) {
            array_push($keys, $pl_key['value']);
            session()->put('search_keywords.' . $pl_key['value'], $pl_key['next_token']);
        }
        $resultsets = $this->fetchVideos($location, $keys, false, 1, true, $keys_used);
        $keys_string = implode(',', $keys);


        return view('search.edit_demo_playlist', compact('selected', 'keys', 'resultsets', 'keys_string', 'videos'));
    }

    public function forgetDemoPlaylist(Request $request)
    {
        session()->forget('selected');
        session()->forget('search_keys');
        session()->forget('search_location');
        session()->forget('video_meta');

        return response()->json(['redirect' => url('login')]);
    }

    public function editPlaylist(Playlist $playlist)
    {
        $this->authorize('update', $playlist);
        // $keys = $request->input('search_key');

        // $location = $playlist->pl_location;

        $owner = $playlist->owner;

        $selected = array_column($playlist->videos->toArray(), 'plv_video_id');

        $pl_keys = $playlist->keys;

        $keys = [];

        foreach ($pl_keys as $pl_key) {
            $keys[] = $pl_key->plk_key;

            session()->put('search_keywords.' . $pl_key->plk_key, $pl_key->plk_next_token);
        }

        $resultsets = $this->fetchVideos($playlist->pl_location, $keys, false, 1, true, $keys_used);

        $keys_string = implode(',', $keys);

        $videos = $playlist->videos;
        $video_ids = $videos->lists('plv_video_id')->toJson();

        return view('search.edit_playlist', compact('playlist', 'selected', 'keys', 'resultsets', 'keys_string', 'owner', 'videos', 'video_ids'));
    }

    public function editPlaylistMore(Playlist $playlist, Request $request)
    {
        $keys[] = $request->input('search_key');

        // $location = $playlist->pl_location;

        $selected = array_column($playlist->videos->toArray(), 'plv_video_id');

        $resultsets = $this->fetchVideos($playlist->pl_location, $keys, true, 1, false);

        return view('search.more_result', compact('resultsets', 'selected', 'playlist'));
    }

    public function editKeywords(Playlist $playlist, Request $request)
    {
        $keys = explode(",", $request->input('search_keys'));

        $resultsets = $this->fetchVideos($playlist->pl_location, $keys, false, 0.5, true, $keys_used);

        // Delete current playlist keys
        $playlist->keys()->delete();

        $playlist->videos()->whereNotIn('keyword', $keys)->delete();
        // Create playlist keys
        $plk = new PlaylistKey;
        if ($keys_used) {
            foreach ($keys_used as $key_used) {
                $plk->create(['plk_playlist' => $playlist->pl_id, 'plk_key' => $key_used['value'], 'plk_weight' => $key_used['weight'], 'plk_next_token' => $key_used['next_token']]);
            }
        }

        return back();
    }

    public function resultsMore(Request $request)
    {
        $keys = $request->input('search_key');

        $location = session()->get('search_location');

        $selected = session()->get('selected', []);

        $resultsets = $this->fetchVideos($location, $keys, true, 1, false);

        return view('search.more_result', compact('resultsets', 'selected'));
    }

    public function add_video(Request $request)
    {
        // Session::put('selected.' . $request->input('id'), $request->input('id'));
        session()->push('selected', $request->input('id'));

        return response()->json(['id' => $request->input('id')]);
    }

    public function remove_video(Request $request)
    {
        $key = array_search($request->input('id'), session()->get('selected'));

        Session::forget('selected.' . $key);

        return response()->json(['id' => $request->input('id')]);
    }

    public function getSelected(Playlist $playlist)
    {
        $videos = $playlist->videos;
        $video_ids = $videos->lists('plv_video_id')->toArray();
        $html = view('search.selected_videos', compact('videos', 'playlist'))->render();

        return response()->json(['html' => $html, 'videos' => $video_ids]);
    }

    public function sortSelected(Request $request)
    {
        $selected = Session::get('selected');

        $start_pos = $request->input('start_pos');

        $end_pos = $request->input('end_pos');

        $this->moveElement($selected, $start_pos, $end_pos);

        Session::put('selected', $selected);

    }

    private function moveElement(&$array, $a, $b)
    {

        $out = array_splice($array, $a, 1);

        array_splice($array, $b, 0, $out);

    }

    public function preview($id)
    {
        $video = $this->vcRepo->where('vc_id', $id)->first();

        $snippet = unserialize($video->vc_snippet);

        $title = $snippet->title;

        return view('search.preview', compact('video', 'snippet', 'title'));
    }

    // public function suggestRegion(Request $request)
    // {
    //   $repoCoun = new Country;
    //
    //   $continents = $repoCoun->continents();
    //
    //   return view('search.suggest_continent', compact('continents'));
    // }

    // Location selection - based on region

    public function suggestLocation(Request $request, $region)
    {
        $page_title = trans('meta_data.search_funnel_title') . ' | ' . $region;

        $page_desc = trans('meta_data.search_funnel_desc');

        $page_img = asset('img/playligo_home_background_glacier.jpg');

        $repoCit = new City;

        $min_hotels = ($region == 'Africa' || $region == 'Oceania') ? 150 : 0;

        $cities = $repoCit->byRegion($region, $min_hotels)->get();

        $chunk_size = config('playligo.max_tags_per_cloud');

        return view('search.suggest_location', compact('cities', 'region', 'page_title', 'page_desc', 'page_img', 'chunk_size'));
    }

}
