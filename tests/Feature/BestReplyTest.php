<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BestReplyTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function a_thread_creator_may_mark_a_reply_as_best_reply()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $replies = create(Reply::class, ['thread_id' => $thread->id], 2);

        $this->assertFalse($replies[1]->isBest());

        $this->postJson(route('best-replies.store', [$replies[1]->id]));

        $this->assertTrue($replies[1]->fresh()->isBest());
    }

    /** @test */
    public function only_thread_creator_may_mark_a_reply_as_best()
    {
        $this->withExceptionHandling()
            ->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $replies = create(Reply::class, ['thread_id' => $thread->id], 2);

        $this->signIn(create(User::class));

        $this->postJson(route('best-replies.store', [$replies[1]->id]))
        ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertFalse($replies[1]->fresh()->isBest());
    }
}
