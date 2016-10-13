<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use App\Traits\ModelTrait;

class User extends Authenticatable
{
    use EntrustUserTrait;
    use ModelTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'facebook_id', 'avatar', 'status', 'remarks', 'invite_code',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function scopeFilter($query, $filter = [])
    {
        if (array_get($filter, 'name')) {
            $query->where('name', 'like', '%' . $filter['name'] . '%');
        }
        if (array_get($filter, 'email')) {
            $query->where('email', 'like', '%' . $filter['email'] . '%');
        }
    }

    public function playlists()
    {
      return $this->hasMany('App\Playlist', 'pl_user', 'id');
    }

    public function polls()
    {
      return $this->hasMany('App\Poll', 'pol_user', 'id');
    }

    public function likes()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_ratings', 'plr_user', 'plr_playlist');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    public function stat()
    {
      $playlist_count = $this->playlists()->count();

      $poll_count = $this->polls()->count();

      return compact('playlist_count', 'poll_count');
    }

    public function owns($related, $field = 'user_id')
    {
        return $this->id == $related->$field;
    }

    public static function boot()
    {
        User::creating(function ($post) {
          $post['status'] = 1;
        });
    }

}
