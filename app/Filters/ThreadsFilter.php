<?php

namespace App\Filters;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ThreadsFilter extends Filters
{
    protected $filters = ['by', 'popular', 'unanswered'];
    
    /**
     * Filter by a username
     *
     * @param string $username
     * @return Builder
     */
    protected function by($username)
    {
        $user = User::where('name', $username)->firstOrFail();

        return $this->builder->where('user_id', $user->id);
    }

    /**
     * Filter threads according to most popular
     *
     * @return Builder
     */
    protected function popular()
    {
        $this->builder->getQuery()->orders = [];

        return $this->builder->orderBy('replies_count', 'DESC');
    }

    protected function unanswered()
    {
        return $this->builder->where('replies_count', 0);
    }
}