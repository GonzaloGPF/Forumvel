<?php

namespace App\Filters;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ThreadsFilter extends Filters
{
    protected $filters = ['by'];
    /**
     * Filter by a username
     * @param string $username
     * @return ThreadsFilter
     * @internal param Builder $builder
     */
    protected function by($username)
    {
        $user = User::where('name', $username)->firstOrFail();

        return $this->builder->where('user_id', $user->id);
    }
}