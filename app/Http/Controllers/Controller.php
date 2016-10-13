<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        // check if user has already left feedback and share this variable between all views
        if (\Auth::user() && session()->get('shouldFeedback')) {
            view()->share('shouldFeedback', \Auth::user()->feedback == null);
        }
        session()->put('last_page', request()->url());
    }

}
