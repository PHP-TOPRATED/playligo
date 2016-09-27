<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoCache extends Model
{
    //
    public $incrementing = false;
    protected $primaryKey = 'vc_id';
    protected $table = 'video_caches';
    protected $fillable = ['vc_id', 'vc_etag', 'vc_kind', 'vc_snippet'];

    public function massCreate(array $batch)
    {
        foreach ($batch as $key => $group) {
          if (!empty($group)) {
            foreach ($group as $item) {
              if (!$this->find($item->id->videoId)) {
                  $this->create(['vc_id' => $item->id->videoId, 'vc_kind' => $item->kind, 'vc_etag' => $item->etag, 'vc_snippet' => serialize($item->snippet) ]);
              }
            }            
          }
        }
    }
}
