<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Playlist;
use App\PlaylistRating;

class PlaylistRatingController extends Controller
{
    public function like(Request $request)
    {
        $plrRepo = new PlaylistRating;

        $playlist = Playlist::wherePlSlug($request->get('playlist'))->firstOrFail();
        $status = null;
        if ($playlist->isLiked()) {
            $playlist->unLike();
            $status = 'unliked';
        } else {
            $playlist->like();
            $status = 'liked';
        }


        return response()->json(['status' => $status]);
    }
}
