<?php

namespace Tests\Feature;

use App\Activity;
use App\Channel;
use App\Reply;
use App\Rules\Recaptcha;
use App\Thread;
use App\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateThreadsTest extends TestCase
{

    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        app()->singleton(Recaptcha::class, function () {
            return \Mockery::mock(Recaptcha::class, function ($mock) {
                $mock->shouldReceive('passes')->andReturn(true);
            });
        });
    }

    /** @test */
    function guests_may_not_create_threads()
    {
        $this->withExceptionHandling();

        $this->get('threads/create')
            ->assertRedirect(route('login'));

        $this->post('threads')
            ->assertRedirect(route('login'));
    }

    /** @test */

    function unauthorized_may_not_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = create(Thread::class);

        $this->delete($thread->path())
            ->assertRedirect('/login');

        $this->signIn();

        $this->delete($thread->path())
            ->assertStatus(403);
    }

    /** @test */
    function authorized_users_can_delete_threads()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => Auth::id()]);

        $reply = create(Reply::class, ['thread_id' => $thread->id]);

        $this->json('DELETE', $thread->path())
            ->assertStatus(204);

        $this->assertDatabaseMissing('threads', ['id' => $thread->id])
            ->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0, Activity::count());
    }

    /** @test */
    public function new_users_must_first_confirm_their_email_address_before_creating_threads()
    {
        $user = factory(User::class)->states('unconfirmed')->create();
        $this->signIn($user);

        $thread = make(Thread::class);

        $this->post(route('threads'), $thread->toArray())
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash', 'You must first confirm your email'); // Maybe delete this message... 
    }

    /** @test */
    function a_user_can_create_new_forum_threads()
    {
        $response = $this->publishThread(['title' => 'Some title', 'body' => 'Some body']);

        $this->get($response->headers->get('Location'))
            ->assertSee('Some title')
            ->assertSee('Some body');
    }

    /** @test */
    function a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    function a_thread_requires_a_valid_channel()
    {
        create(Channel::class);

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 4])
            ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    public function a_thread_requires_recaptcha_verification()
    {
        unset(app()[Recaptcha::class]); // To remove the Mock

        $this->publishThread(['g-recaptcha-response' => 'test'])
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    /** @test */
    function a_thread_requires_a_unique_slug()
    {
        $this->signIn();

        $thread = create(Thread::class, ['title' => 'Test Thread']);

        $this->assertEquals('test-thread', $thread->fresh()->slug);

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'test'])->json();

        $this->assertEquals("test-thread-{$thread['id']}", $thread['slug']);
    }

    /** @test */
    public function a_thread_with_a_title_that_ends_in_a_number_should_generate_proper_slug()
    {
        $this->signIn();

        $thread = create(Thread::class, ['title' => 'Test Thread 24']);

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'test'])->json();

        $this->assertEquals("test-thread-24-{$thread['id']}", $thread['slug']);
    }

    function publishThread($attributes = [])
    {
        $this->withExceptionHandling();
        $this->signIn();

        $thread = make(Thread::class, $attributes);

        return $this->post(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'test']);
    }
}
