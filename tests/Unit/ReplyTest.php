<?php

namespace Tests\Unit;

use App\Reply;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReplyTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function it_has_an_owner()
    {
        $reply = create(Reply::class);

        $this->assertInstanceOf(User::class, $reply->owner);
    }

    /** @test */
    public function it_knows_it_was_just_published()
    {
        $reply = create(Reply::class);

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    /** @test */
    public function it_can_detect_all_mentioned_users_in_the_body()
    {
        $reply = create(Reply::class, [
            'body' => '@Zalo is mentioned. We will mention @Bob too.'
        ]);

        $this->assertEquals(['Zalo', 'Bob'], $reply->mentionedUsers());
    }

    /** @test */
    public function it_wraps_mentioned_users_in_the_body_within_anchor_tags()
    {
        $reply = create(Reply::class, [
            'body' => 'Hello @Zalo.'
        ]);

        $this->assertEquals(
            'Hello <a href="/profiles/Zalo">@Zalo</a>.',
            $reply->body
        );
    }
    
    /** @test */
    public function it_knows_if_it_is_the_best_reply()
    {
        $reply = create(Reply::class);

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->fresh()->isBest());
    }

    /** @test */
    function a_reply_body_is_sanitized_automatically()
    {
        $reply = make(Reply::class, ['body' => '<script>alert(\'bad\');</script><p>This is ok.</p>']);

        $this->assertEquals('<p>This is ok.</p>', $reply->body);
    }
}
