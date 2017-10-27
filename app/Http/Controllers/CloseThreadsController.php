<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class CloseThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    public function store(Thread $thread)
    {
        $thread->update(['closed' => true]);
    }

    public function destroy(Thread $thread)
    {
        $thread->update(['closed' => false]);
    }
}
