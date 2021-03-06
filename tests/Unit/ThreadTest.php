<?php

namespace Tests\Unit;

use App\Channel;
use App\Notifications\ThreadWasUpdated;
use App\Thread;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * @var Thread
     */
    protected $thread;

    protected function setUp()
    {
        parent::setUp();
        $this->thread = create(Thread::class);
    }

    /** @test */
    function a_thread_has_a_path()
    {
        $this->assertEquals("/threads/{$this->thread->channel->slug}/{$this->thread->slug}", $this->thread->path());
    }

    /** @test */
    function a_thread_has_a_creator()
    {
        $this->assertInstanceOf(User::class, $this->thread->creator);
    }

    /** @test */
    function a_thread_has_replies()
    {
        $this->assertInstanceOf(Collection::class, $this->thread->replies);
    }

    /** @test */
    function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'foobar',
            'user_id' => 1
        ]);
        $this->assertCount(1, $this->thread->fresh()->replies);
    }

    /** @test */
    function a_thread_belongs_to_a_channel()
    {
        $this->assertInstanceOf(Channel::class, $this->thread->channel);
    }

    /** @test */
    function a_thread_can_be_subscribed_to()
    {
        $this->thread->subscribe($userId = 1);

        $this->assertEquals(1, $this->thread->subscriptions()->where('user_id', $userId)->count());
    }

    /** @test */
    function a_thread_can_be_unsubscribe_from()
    {
        $this->thread->subscribe($userId = 1);
        $this->thread->unsubscribe($userId);

        $this->assertCount(0, $this->thread->subscriptions);
    }

    /** @test */
    function it_knows_if_authenticated_users_is_subscribed_to_it()
    {
        $this->signIn();

        $this->assertFalse($this->thread->isSubscribed);

        $this->thread->subscribe();

        $this->assertTrue($this->thread->isSubscribed);
    }

    /** @test */
    function a_thread_notifies_all_subscribers_when_a_reply_is_added()
    {
        Notification::fake();

        $this->signIn();

        $this->thread
            ->subscribe()
            ->addReply([
            'body' => 'foobar',
            'user_id' => create(User::class)->id
        ]);

//        $this->assertDatabaseHas('notifications', ['notifiable_id' => auth()->id()]);
        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    /** @test */
    function a_thread_can_check_if_authenticated_user_has_read_all_replies()
    {
        $this->signIn();

        $this->assertTrue($this->thread->hasUpdatesFor());

        $this->thread->readByUser();

        $this->assertFalse($this->thread->hasUpdatesFor());
    }

    /** @test */
    function a_thread_body_is_sanitized_automatically()
    {
        $thread = make(Thread::class, ['body' => '<script>alert(\'bad\');</script><p>This is ok.</p>']);

        $this->assertEquals('<p>This is ok.</p>', $thread->body);
    }
    
//    /** @test */
//    function a_thread_records_each_visit()
//    {
//        $thread = make(Thread::class, ['id' => 1]);
//
//        $thread->resetVisits();
//
//        $this->assertSame(0, $thread->visits());
//
//        $thread->recordVisit();
//
//        $this->assertEquals(1, $thread->visits());
//
//        $thread->recordVisit();
//
//        $this->assertEquals(2, $thread->visits());
//    }
}
