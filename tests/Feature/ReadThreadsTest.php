<?php

namespace Tests\Feature;

use App\Channel;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReadThreadsTest extends TestCase
{

    use DatabaseMigrations;

    private $thread;

    protected function setUp()
    {
        parent::setUp();
        $this->thread = factory(Thread::class)->create();
    }

    /** @test */
    function a_user_can_view_all_threads()
    {

        $this->get('/threads')
            ->assertSee($this->thread->title);
    }

    /** @test */
    function a_user_can_view_single_thread()
    {

        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    /** @test */
    function a_user_can_read_replies_that_are_associated_with_a_thread()
    {
        $reply = create(Reply::class, ['thread_id' => $this->thread->id]);

        $this->get($this->thread->path())
            ->assertSee($reply->body);
    }

    /** @test */
    function a_user_can_filter_threads_according_to_a_channel()
    {
        $channel = create(Channel::class);

        $threadInChannel = create(Thread::class, ['channel_id' => $channel->id]);
        $threadNotInChannel = create(Thread::class);

        $this->get('threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    function a_user_can_filter_threads_by_username()
    {
        $this->signIn(create(User::class, ['name' => 'Bob']));

        $threadByBob = create(Thread::class, ['user_id' => Auth::id()]);
        $threadByNotBob = create(Thread::class);

        $this->get('/threads?by=Bob' )
            ->assertSee($threadByBob->title)
            ->assertDontSee($threadByNotBob->title);
    }

    /** @test */
    function a_user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = create(Thread::class);
        create(Reply::class, ['thread_id' => $threadWithTwoReplies->id ], 2);

        $threadWithThreeReplies = create(Thread::class);
        create(Reply::class, ['thread_id' => $threadWithThreeReplies->id], 3);

        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('threads?popular=1')->json();

        $this->assertEquals([3, 2, 0], array_slice(array_column($response, 'replies_count'), 0, 3));
    }
}
