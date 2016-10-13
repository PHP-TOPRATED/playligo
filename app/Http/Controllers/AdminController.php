<?php

namespace App\Http\Controllers;

use App\Feedback;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;

class AdminController extends Controller
{
    public function index()
    {
      $repoUser = new User;

      $users = $repoUser->count('id');

      return view('admin.dashboard', compact('users'));
    }

    public function feedbacks()
    {
        $feedbacks = Feedback::orderBy('created_at', 'desc')->get();
        return view('admin.feedbacks.list', compact('feedbacks'));
    }
}
