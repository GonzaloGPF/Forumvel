<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class NotificationsTest extends TestCase
{

    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    function a_notification_is_prepare_when_a_subscribed_thread_receives_a_new_reply_that_is_not_current_user()
    {
        $thread = create(Thread::class)->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        $thread->fresh()->addReply([
            'user_id' => auth()->id(),
            'body' => 'Some body'
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $thread->fresh()->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Some body'
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    public function a_user_can_fetch_their_unread_notifications()
    {
        create(DatabaseNotification::class);

        $response = $this->getJson("profiles/". auth()->user()->name. "/notifications")->json();

        $this->assertCount(1, $response);
    }

    /** @test */
    public function a_user_can_mark_notification_as_read()
    {
        create(DatabaseNotification::class);

        $user = auth()->user();

        $this->assertCount(1, $user->unreadNotifications);

        $notificationId = $user->unreadNotifications->first()->id;

        $this->delete("profiles/$user->name/notifications/$notificationId");

        $this->assertCount(0, $user->fresh()->unreadNotifications);
    }
}
