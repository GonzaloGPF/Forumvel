<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MentionsUserTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function mentioned_users_in_a_reply_are_notified()
    {
        $zalo = create(User::class, ['name' => 'Zalo']);

        $this->signIn($zalo);

        $bob = create(User::class, ['name' => 'Bob']);

        $thread = create(Thread::class);

        $reply = make(Reply::class, [
            'body' => 'Mentioning @Zalo in a comment. @Brity is also mentioned'
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $zalo->notifications);
    }

    /** @test */
    public function it_can_fetch_all_mentioned_users_starting_with_the_given_characters()
    {
        create(User::class, ['name' => 'Professor Bob']);
        create(User::class, ['name' => 'Prof John']);
        create(User::class, ['name' => 'Pro Player William']);

        $results = $this->json('GET', '/api/users', ['name' => 'Prof']);

        $this->assertCount(2, $results->json());
    }
}
