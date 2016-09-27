<?php

namespace App\Services\GetYourGuide\Facade;


use Illuminate\Support\Facades\Facade;

class GetYourGuide extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'getyourguide';
    }
}