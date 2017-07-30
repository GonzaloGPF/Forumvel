<?php

namespace App;


use Illuminate\Support\Facades\Auth;

trait Favoritable
{

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    public function favorite()
    {
        $attributes = ['user_id' => Auth::id()];

        if (!$this->favorites()->where($attributes)->exists()) {
            // Because it's a polimorphic relationship it only needs 'user_id',
            // it will fetch 'favorited_id' and 'favorited_type' automatically from $attributes
            return $this->favorites()->create($attributes);
        }
    }

    public function isFavorited()
    {
        // $this->favorites now will return a collection because of $with property
        return !!$this->favorites->where('user_id', Auth::id())->count();
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }
}