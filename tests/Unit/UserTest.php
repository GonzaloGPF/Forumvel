<?php

namespace Tests\Feature;

use App\Reply;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function a_user_can_fetch_the_most_recent_reply()
    {
        $user = create(User::class);

        $reply = create(Reply::class, ['user_id' => $user->id]);

        $this->assertEquals($user->lastReply->id, $reply->id);
    }
}
