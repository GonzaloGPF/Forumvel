<?php

namespace Tests\Feature;

use App\Mail\PleaseConfirmYourEmail;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegistrationTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();

        $this->post(route('register'), [
            'name' => 'Zalo',
            'email' => 'zalo@mail.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        Mail::assertQueued(PleaseConfirmYourEmail::class);
    }
    
    /** @test */
    public function users_can_fully_confirm_their_email_address()
    {
        Mail::fake(); // We place Mail::fake() just to make test faster (avoid sending email)

        $this->post(route('register'), [
            'name' => 'Zalo',
            'email' => 'zalo@mail.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $user = User::whereName('Zalo')->first();

        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);

        $this->get(route('register.confirm', ['token' => $user->confirmation_token]))
            ->assertRedirect(route('threads'));

        tap($user->fresh(), function($user) {
            $this->assertTrue($user->confirmed);
            $this->assertNull($user->confirmation_token);
        });
    }

    /** @test */
    public function confirming_invalid_token()
    {
        $this->get(route('register.confirm', ['token' => 'invalid']))
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash');
    }
}
