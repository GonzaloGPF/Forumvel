<?php

namespace Tests\Unit;

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
    
    /** @test */
    public function a_user_can_determine_their_avatar_path()
    {
        $user = create(User::class);

        $this->assertEquals(asset('images/avatars/default.png'), $user->avatar_path);

        $user = create(User::class, ['avatar_path' => 'avatars/me.jpg']);

        $this->assertEquals(asset('storage/avatars/me.jpg'), $user->avatar_path);
    }
}
