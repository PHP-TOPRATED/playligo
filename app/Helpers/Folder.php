<?php

namespace App\Helpers;

use File;
use Image;
use Illuminate\Database\Eloquent\Model;

class Folder
{

    const KEYWORDS_DIR = 'uploads/keywords/';
    const KEYWORDS_THUMB_DIR = 'uploads/keywords/thumb/';

    /**
     * Check folder existence
     *
     * @param $dir
     * @param array|null $thumb
     */
    public static function checkDirectory($dir, $thumb = [])
    {
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 493, true);
        }
        if ($thumb['folder'] && !File::isDirectory($thumb['folder'])) {
            File::makeDirectory($thumb['folder'], 493, true);
        }
    }

    /**
     * Save simple image
     *
     * @param Model $model
     * @param $folder
     * @param $file
     * @param array $thumb
     */
    public static function saveImage(Model $model, $folder, $file, $thumb = [])
    {
        Folder::checkDirectory($folder, $thumb);
        Folder::checkOldImage($model, $folder);
        $img = Image::make($file);
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $img->save($folder . $filename);
        if ($thumb) {
            $img->fit($thumb['width'], $thumb['height'])->save($thumb['folder'] . $filename);
        }

        $model->update(['image' => $filename]);
    }

    /**
     * Remove old image if exists
     *
     * @param Model $model
     * @param $folder
     * @param array $thumb
     */
    public static function checkOldImage(Model $model, $folder, $thumb = [])
    {
        $image = $model->getAttribute('image');
        if (isset($image) && File::exists($folder . $image)) {
            File::delete($folder . $image);
            if($thumb && File::exists($thumb['folder'] . $image)) {
                File::delete($thumb['folder'] . $image);
            }
        }
    }

    /**
     * Remove image if exists
     *
     * @param Model $model
     */
    public static function removeImage(Model $model)
    {
        $filepath = $model->getAttribute('image_path');
        $thumbpath = $model->getAttribute('thumb_path');
        if (!empty($filepath) && File::exists($filepath)) {
            File::delete($filepath);
            if (!empty($thumbpath) && File::exists($thumbpath)) {
                File::delete($thumbpath);
            }
        }
    }
}