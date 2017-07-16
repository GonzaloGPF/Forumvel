<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepliesController extends Controller
{

    /**
     * RepliesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Thread $thread)
    {
        $reply = $thread->addReply([
            'user_id' => Auth::id(),
            'body' => request('body')
        ]);
        return redirect($thread->path());
    }
}
