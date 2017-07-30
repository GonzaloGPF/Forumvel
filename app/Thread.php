<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Thread extends Model
{
    use RecordsActivity;

    protected $fillable = [
        'title', 'body' , 'user_id', 'channel_id'
    ];

    protected $with = ['creator', 'channel'];

    protected static function boot()
    {
        parent::boot();

        // A query scope that is applied to all queries
        static::addGlobalScope('repliesCount', function(Builder $builder){
            $builder->withCount('replies'); // will add a new attribute to every Thread called 'replies_count'
        });

        static::deleting(function(Thread $thread){
            $thread->replies()->delete();
        });
    }

    public function path($extra = null)
    {
        $extra = $extra == null ? '' : '/' . $extra;
        return "/threads/{$this->channel->slug}/{$this->id}" . $extra;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class)
            ->with('owner'); // every Reply will eager load his owner
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function addReply($reply)
    {
        $this->replies()->create($reply);
    }

    public function scopeFilter(Builder $query, $filters)
    {
        return $filters->apply($query);
    }
}
