<?php

namespace App;

use App\Helpers\Folder;
use App\Traits\ModelTrait;
use ErrorException;
use File;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    use ModelTrait;

    protected $table = 'keywords';
    protected $guarded = ['id'];


    protected $appends = [
        'image_path',
        'thumb_path'
    ];

    const KEYWORD_ICON_WIDTH = 200;
    const KEYWORD_ICON_HEIGHT = 150;
    const MAX_KEYWORDS = 50;

    /**
     * Return path of image if it exists
     *
     * @return null|string
     */
    public function getImagePathAttribute()
    {
        try {
            $filename = $this->getAttribute('image');
        } catch (ErrorException $ex) {
            $filename = null;
        }
        $filepath = Folder::KEYWORDS_DIR . $filename;

        return (!empty($filename)
            && File::exists($filepath)
            && File::isFile($filepath))
            ? $filepath
            : null;
    }

    /**
     * Return path of thumb if it exists
     *
     * @return null|string
     */
    public function getThumbPathAttribute()
    {
        try {
            $filename = $this->getAttribute('image');
        } catch (ErrorException $ex) {
            $filename = null;
        }
        $thumbpath = Folder::KEYWORDS_THUMB_DIR . $filename;

        return (!empty($filename)
            && File::exists($thumbpath)
            && File::isFile($thumbpath))
            ? $thumbpath
            : null;
    }

    /**
     * Filter list of keywords
     *
     * @param $query
     * @param array $filter
     */
    public function scopeFilter($query, $filter = [])
    {
        if (array_get($filter, 'name')) {
            $query->where('name', 'like', '%' . $filter['name'] . '%');
        }
        if (array_get($filter, 'description')) {
            $query->where('description', 'like', '%' . $filter['description'] . '%');
        }
    }

    /**
     * Returns url of google maps place photo
     */
    public static function getGooglePlacePhotoUrl($keyword)
    {
        $photo_url = null;
        if (isset($keyword->photos)) {
            $photo_object = array_shift($keyword->photos);
            $photo_url = url(config('googlemaps.service.placephoto.url')
                . 'key=' . config('googlemaps.key')
                . '&photoreference=' . $photo_object->photo_reference
                . '&maxheight=' . Keyword::KEYWORD_ICON_HEIGHT
                . '&maxwidth=' . Keyword::KEYWORD_ICON_WIDTH);
        } else {
            $photo_url = url($keyword->icon);
        }
        return $photo_url;

    }
}
