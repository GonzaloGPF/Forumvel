<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path', 'confirmation_token', 'confirmed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email'
    ];

    protected $casts = [
        'confirmed' => 'boolean'
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    public function isAdmin()
    {
        return in_array($this->email, ['admin@mail.com']);
    }

    public function confirm()
    {
        $this->update([
            'confirmed' => true,
            'confirmation_token' => null
        ]);
    }

    public function visitedThreadCacheKey($thread)
    {
        return sprintf("users.%s.visits.%s", $this->id, $thread->id);
    }

    public function read(Thread $thread)
    {
        cache()->forever(
            $this->visitedThreadCacheKey($thread),
            Carbon::now()
        );
    }

    public function lastReply()
    {
        return $this->hasOne(Reply::class)->latest();
    }

    public function getAvatarPathAttribute($avatar)
    {
        return asset($avatar ? "storage/$avatar" : 'images/avatars/default.png');
    }
}
