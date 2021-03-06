<?php

namespace App;

use App\Events\ThreadReceivedNewReply;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Stevebauman\Purify\Facades\Purify;

class Thread extends Model
{
    use RecordsActivity, Searchable;

    protected $fillable = [
        'slug', 'title', 'body' , 'user_id', 'channel_id', 'best_reply_id', 'closed'
    ];

    protected $casts = [
        'closed' => 'boolean'
    ];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribed'];

    protected static function boot()
    {
        parent::boot();

        // A query scope that is applied to all queries
//        static::addGlobalScope('repliesCount', function(Builder $builder){
//            $builder->withCount('replies'); // will add a new attribute to every Thread called 'replies_count'
//        });

        static::created(function(Thread $thread){
            $thread->update(['slug' => $thread->title]);
//            $thread->creator->increment('reputation', 10);
            Reputation::award($thread->creator, Reputation::THREAD_WAS_PUBLISHED);
            // if dont like static, you can use (new Reputation)->award(...)
        });

        static::deleting(function(Thread $thread){
            $thread->replies->each->delete();
            Reputation::reduce($thread->creator, Reputation::THREAD_WAS_PUBLISHED);
        });
    }

    public function getBodyAttribute($body)
    {
        return Purify::clean($body);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * This methods indicates Algolia's what attributes will be exposed.
     * In this case, we will keep everything and will add a new 'path' attribute that will be useful for front end
     */
    public function toSearchableArray()
    {
        return $this->toArray() + ['path' => $this->path()];
    }

    /**
     * @param null $extra
     * @return string
     */
    public function path($extra = null)
    {
        $extra = $extra == null ? '' : '/' . $extra;
        return "/threads/{$this->channel->slug}/{$this->slug}" . $extra;
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
        $reply = $this->replies()->create($reply);
        event(new ThreadReceivedNewReply($reply));
        return $reply;
    }

    public function notifyUsers($reply)
    {
        $this->subscriptions()
            ->where('user_id', '!=', $reply->user_id)
            ->get()
            ->each->notify($reply);
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

    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);
        return $this;
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    public function getIsSubscribedAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function hasUpdatesFor(User $user = null)
    {
        $user = $user ?: \auth()->user();
        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }

    public function readByUser(User $user = null)
    {
        $user = $user ?: \auth()->user();
        cache()->forever($user->visitedThreadCacheKey($this), Carbon::now());
    }

    public function setSlugAttribute($title)
    {
        $slug = str_slug($title);

        if(static::whereSlug($slug)->exists()) {
            $slug = "{$slug}-" . $this->id;
        }

        $this->attributes['slug'] = $slug;
    }

    public function markBestReply(Reply $reply)
    {
        $this->update(['best_reply_id' => $reply->id]);
        Reputation::award($reply->owner, Reputation::BEST_REPLY_AWARD);
    }
}
