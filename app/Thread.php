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
            $thread->replies->each->delete();
        });
    }

    /**
     * @param null $extra
     * @return string
     */
    public function path($extra = null)
    {
        $extra = $extra == null ? '' : '/' . $extra;
        return "/threads/{$this->channel->slug}/{$this->id}" . $extra;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class)
            ->with('owner'); // every Reply will eager load his owner
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * @param $reply
     * @return Model
     */
    public function addReply($reply)
    {
        return $this->replies()->create($reply);
    }

    /**
     * @param Builder $query
     * @param $filters
     * @return mixed
     */
    public function scopeFilter(Builder $query, $filters)
    {
        return $filters->apply($query);
    }
}
