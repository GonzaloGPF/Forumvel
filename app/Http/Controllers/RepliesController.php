<?php

namespace App\Http\Controllers;

use App\Inspections\Spam;
use App\Reply;
use App\Thread;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class RepliesController extends Controller
{

    /**
     * RepliesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    public function store($channelId, Thread $thread)
    {

        if(Gate::denies('create', new Reply)){
            return response('Your are postin too frequently, please take a break :)', Response::HTTP_TOO_MANY_REQUESTS);
        };
        try{
            $this->validate(request(), ['body' => 'required|spamfree']);

            $reply = $thread->addReply([
                'user_id' => Auth::id(),
                'body' => request('body')
            ]);
        } catch (Exception $e) {
            return response('Sorry, your Reply could not be saved at this time', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $reply->load('owner');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);
        try {

            $this->validate(request(), ['body' => 'required|spamfree']);

            $reply->update(request()->all());
        } catch (Exception $e) {
            return response('Sorry, your Reply could not be saved at this time', 422);
        }
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if(request()->expectsJson()){
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }
}
