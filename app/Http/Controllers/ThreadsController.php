<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Filters\ThreadsFilter;
use App\Inspections\Spam;
use App\Thread;
use App\Trending;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ThreadsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('email-confirmed')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Channel $channel
     * @param ThreadsFilter $filters
     * @param Trending $trending
     * @return \Illuminate\Http\Response
     * @internal param ThreadsFilter $filter
     */
    public function index(Channel $channel, ThreadsFilter $filters, Trending $trending)
    {
        $threads = $this->getThreads($channel, $filters);

        if(request()->wantsJson()){
            return $threads;
        }

        return view('threads.index', [
            'threads' => $threads,
            'trending' => $trending->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
            'channel_id' => 'required|exists:channels,id'
        ]);

        $thread = Thread::create([
            'user_id' => Auth::id(),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body'),
        ]);

        if(request()->wantsJson()){
            return response($thread, Response::HTTP_CREATED);
        }

        return redirect($thread->path())
            ->with('flash', 'Your thread has been created');
    }

    /**
     * Display the specified resource.
     *
     * @param $channel
     * @param  \App\Thread $thread
     * @param Trending $trending
     * @return \Illuminate\Http\Response
     */
    public function show($channel, Thread $thread, Trending $trending)
    {
        if(auth()->check()){
            auth()->user()->read($thread);
        }

        $trending->push($thread);

        $thread->increment('visits_count');

        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        if(request()->wantsJson()){
            return response([], 204);
        }

        return redirect('/threads');
    }

    /**
     * @param Channel $channel
     * @param ThreadsFilter $filters
     * @return mixed
     */
    protected function getThreads(Channel $channel, ThreadsFilter $filters)
    {
        $threads = Thread::latest()->filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        return $threads->paginate(15);
    }
}
