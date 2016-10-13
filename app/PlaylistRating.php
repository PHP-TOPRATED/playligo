<?php

namespace App;

use App\Playlist;

use Illuminate\Database\Eloquent\Model;

class PlaylistRating extends Model
{
    protected $fillable = ['plr_playlist', 'plr_user'];

}
