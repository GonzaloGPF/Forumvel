@component('mail::message')
# One last step

We just need you to confirm your email address to prove that you are a human. Thanks!

@component('mail::button', [ 'url' => url('/register/confirm?token=' . $user->confirmation_token) ])
 Confirm Email
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
