<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CloseThreadTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function non_administrator_may_not_close_threads()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->user()->id]);

        $this->post(route('closed-threads.store', $thread))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertFalse($thread->fresh()->closed);
    }

    /** @test */
    public function an_administrator_can_close_threads()
    {
        $user = factory(User::class)->states('admin')->create();
        $this->signIn($user);

        $thread = create(Thread::class, ['user_id' => $user->id]);

        $this->post(route('closed-threads.store', $thread))
            ->assertStatus(Response::HTTP_OK);

        $this->assertTrue($thread->fresh()->closed, 'Failed asserting that thread is closed');
    }

    /** @test */
    public function an_administrator_can_open_a_closed_thread()
    {
        $user = factory(User::class)->states('admin')->create();
        $this->signIn($user);

        $thread = create(Thread::class, [
            'user_id' => $user->id,
            'closed' => true
        ]);

        $this->delete(route('closed-threads.destroy', $thread));

        $this->assertFalse($thread->fresh()->closed, 'Failed asserting that thread is open');
    }

    /** @test */
    public function once_closed_a_thread_may_not_receive_new_replies()
    {
        $this->signIn();

        $thread = create(Thread::class, ['closed' => true]);
        $reply = make(Reply::class, ['user_id' => auth()->id()]);

        $this->post($thread->path('replies'), $reply->toArray())
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
