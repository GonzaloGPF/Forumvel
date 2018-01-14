<?php

namespace App;

class Reputation
{
    const THREAD_WAS_PUBLISHED = 10;
    const REPLY_POSTED = 2;
    const BEST_REPLY_AWARD = 50;
    const REPLY_FAVORITED = 5;

    static function award(User $user, $points)
    {
        $user->increment('reputation', $points);
    }

    static function reduce(User $user, $points)
    {
        $user->decrement('reputation', $points);
    }
}