<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Playlist;
use App\PlaylistVideo;
use Auth;
use Session;

class PlaylistVideoController extends Controller
{
    protected $plRepo;
    protected $plvRepo;

    public function __construct(Playlist $plRepo, PlaylistVideo $plvRepo)
    {
        $this->plRepo = $plRepo;
        $this->plvRepo = $plvRepo;
    }

    public function store(Request $request)
    {
        $input = $request->input();

        $created = $this->plvRepo->create([
            'plv_playlist' => array_get($input, 'pl_id'),
            'plv_video_id' => array_get($input, 'id'),
            'keyword'      => array_get($input, 'keyword'),
            'plv_order' => array_get($input, 'plv_order')
        ]);

        return response()->json(['created' => $created]);
    }


    public function delete(PlaylistVideo $video)
    {
        return view('playlistvideo.delete', compact('video'));
    }

    public function destroy(Request $request)
    {
        $input = $request->input();
        $this->plvRepo->where('plv_playlist', array_get($input, 'pl_id'))->where('plv_video_id', array_get($input, 'id'))->delete();

        return response()->json(['status' => trans('messages.delete_successful')]);
    }

    public function instantDestroy(Request $request)
    {
        $deleted = $this->plvRepo->find($request->input('plv_id'))->delete();

        return response()->json(['deleted' => $deleted]);
    }

}
