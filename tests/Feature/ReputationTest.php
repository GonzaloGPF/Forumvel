<?php

namespace Tests\Feature;

use App\Reply;
use App\Reputation;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReputationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_user_earns_points_when_they_create_a_thread()
    {
        $thread = create(Thread::class);

        $this->assertEquals(Reputation::THREAD_WAS_PUBLISHED, $thread->creator->reputation);
    }

    /** @test */
    function a_user_lose_points_when_they_delete_a_thread()
    {
        $this->signIn();
        $thread = create(Thread::class, ['user_id' => auth()->user()->id]);

        $this->assertEquals(Reputation::THREAD_WAS_PUBLISHED, $thread->creator->reputation);

        $this->delete($thread->path());

        $this->assertEquals(0, $thread->creator->fresh()->reputation);
    }

    /** @test */
    function a_user_earns_points_when_they_reply_to_a_thread()
    {
        $thread = create(Thread::class);

        $reply = $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Some reply'
        ]);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->reputation);
    }

    /** @test */
    function a_user_loses_points_when_they_reply_to_a_thread_is_deleted()
    {
        $this->signIn();
        $reply = create(Reply::class, ['user_id' => auth()->user()->id]);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->reputation);

        $this->delete("/replies/$reply->id");

        $this->assertEquals(0, $reply->owner->fresh()->reputation);
    }

    /** @test */
    function a_user_earns_points_when_their_reply_is_marked_as_best()
    {
        $thread = create(Thread::class);

        $reply = $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Some reply'
        ]);

        $thread->markBestReply($reply);

        $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARD;
        $this->assertEquals($total, $reply->owner->reputation);
    }

    /** @test */
    function a_user_earns_points_when_their_reply_is_favorited()
    {
        $this->signIn();
        $thread = create(Thread::class);

        $reply = $thread->addReply([
            'user_id' => auth()->user()->id,
            'body' => 'Some reply'
        ]);

        $this->post("/replies/$reply->id/favorites");

        $total = Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED;
        $this->assertEquals($total, $reply->owner->fresh()->reputation);
    }

    /** @test */
    function a_user_earns_points_when_their_favorited_reply_is_unfavorited()
    {
        $this->signIn();
        $reply = create(Reply::class, ['user_id' => auth()->user()->id]);

        $this->post("/replies/$reply->id/favorites");

        $total = Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED;
        $this->assertEquals($total, $reply->owner->fresh()->reputation);

        $this->delete("/replies/$reply->id/favorites");

        $total = Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED - Reputation::REPLY_FAVORITED;
        $this->assertEquals($total, $reply->owner->fresh()->reputation);
    }
}