<?php

namespace App\Http\Controllers;

use App\Feedback;
use Illuminate\Http\Request;

use App\Http\Requests;

class FeedbackController extends Controller
{
    public function create(Request $request)
    {
        Feedback::create([
            'user_id' => $request->input('user'),
            'mark'    => $request->input('mark'),
            'comment' => $request->input('comment')
        ]);
        return response()->json('success');
    }
}
