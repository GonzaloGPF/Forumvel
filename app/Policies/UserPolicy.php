<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Symfony\Component\HttpKernel\Profiler\Profile;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can update the thread.
     *
     * @param  \App\User $user
     * @param \App\User $signInUser
     * @return mixed
     * @internal param Profile $profile
     */
    public function update(User $user, User $signInUser)
    {
        return $user->id === $signInUser->id;
    }
}
