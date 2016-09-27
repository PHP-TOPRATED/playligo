<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditPlaylist;
use App\Playlist;
use App\PlaylistVideo;
use App\Traits\ControllerTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class PlaylistController extends Controller
{
    protected $plRepo;
    protected $plvRepo;

    use ControllerTrait;

    public function __construct(Playlist $plRepo, PlaylistVideo $plvRepo)
    {
        parent::__construct();

        $this->plRepo = $plRepo;

        $this->plvRepo = $plvRepo;

        $this->parm['search'] = 'src_playlist';
    }

    public function index(Request $request)
    {
        $playlists = $this->plRepo->filterOwner($request->user()->id)->orderBy('pl_id', 'desc')->getPaginated();

        return view('playlist.list', compact('playlists'));
    }

    public function adminList(Request $request)
    {
        $search = session()->get($this->parm['search']);

        $playlists = $this->plRepo->select('playlists.*')->filter($search)->withOwner()->orderBy('pl_id', 'desc')->getPaginated();

        $filter = 'admin.playlist.filter';

        $total_record = $playlists->total();

        $page_title = trans('playlist.list');

        return view('admin.playlist.list', compact('playlists', 'filter', 'search', 'total_record', 'page_title'));
    }

    public function store(Request $request)
    {
        $playlist = $this->plRepo->create(['pl_user' => Auth::user()->id, 'pl_title' => $request->input('pl_title')]);

        $this->plvRepo->massCreate($playlist->pl_id, Session::get('selected', []), Session::get('search_keys'));

        Session::forget('selected');

        return redirect('playlist/successful/' . $playlist->pl_id)->with('status', trans('messages.playlist_create_successful'));

        // return back()->with('status', trans('messages.playlist_create_successful'));
    }

    public function store2(Request $request)
    {
        $playlist = $this->plRepo->create(['pl_user' => Auth::user()->id, 'pl_title' => $request->input('pl_title')]);

        $this->plvRepo->massCreate($playlist->pl_id, Session::get('selected', []), Session::get('search_keys'));

        Session::forget('selected');

        return back()->with('status', trans('messages.playlist_create_successful'));
    }

    public function delete(Playlist $playlist)
    {
        $this->authorize('update', $playlist);

        return view('playlist.delete', compact('playlist'));
    }

    public function destroy(Request $request)
    {
        $playlist = $this->plRepo->find($request->input('pl_id'));

        $this->authorize('update', $playlist);

        $playlist->delete();

        return back()->with('status', trans('messages.delete_successful'));
    }

    public function edit(Playlist $playlist)
    {
        // $this->authorize('update', $prop);

        return view('playlist.edit', compact('playlist'));
    }

    public function update(EditPlaylist $request)
    {
        $input = $request->all();

        $this->plRepo->find($request->input('pl_id'))->update($input);

        return response()->json(['message' => trans('messages.store_successful')]);
    }

    public function successful(Playlist $playlist)
    {
        $dd_polls = \App\Poll::filterActive()->filterOwner(auth()->user()->id)->toDropDown('pol_id', 'pol_title');

        $disabled = \App\Poll::filterActive()->filterOwner(auth()->user()->id)->count() == 0 ? 'disabled' : '';

        return view('playlist.successful', compact('playlist', 'dd_polls', 'disabled'));
    }

    public function sortItem(Request $request)
    {
        $input = $request->input();

        $this->plvRepo->reorder($input['id'], $input['pl_id'], $input['start_pos'], $input['end_pos']);
    }

    public function loadPlaylist(Playlist $playlist)
    {
        return view('playlist.poll_playlist_videos', compact('playlist'));

    }

    public function preview(Playlist $playlist)
    {
        $videos = $playlist->videos;

        return view('playlist.preview', compact('videos', 'playlist'));
    }

    /**
     * Publish playlist
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publish(Request $request)
    {
        $playlist = Playlist::findOrFail($request->input('id'));
        $this->authorize('update', $playlist);
        $status = true;
        // playlist can not be unpublished
        if ($playlist->isPublished()) {
            return response()->json([
                'message' => trans('messages.can_not_unpublish')
            ]);
        }
        $playlist->update(['pl_status' => $status]);

        return response()->json([
            'message'  => trans('messages.publish_successful'),
            'redirect' => route('public_playlist.view', ['pl_slug' => $playlist->pl_slug, 'share' => true])
        ]);
    }

}
