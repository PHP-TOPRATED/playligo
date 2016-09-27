<?php

namespace App\Services\GetYourGuide\Entities;


class Tour extends Model
{
    protected $guarded = ['tour_id'];

    protected $appends = [
        'name',
        'description',
        'image_path'
    ];

    public function getNameAttribute()
    {
        return $this->getAttribute('title');
    }

    public function getDescriptionAttribute()
    {
        return $this->getAttribute('abstract');
    }

    public function getImagePathAttribute()
    {
        $pictures = $this->getAttribute('pictures');
        $image = array_shift($pictures)['url'];
        return str_replace('[format_id]', '58', $image);
    }
}