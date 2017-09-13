<?php

namespace App;

use App\Events\ThreadReceivedNewReply;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;

    protected $fillable = [
        'slug', 'title', 'body' , 'user_id', 'channel_id'
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

        static::deleting(function(Thread $thread){
            $thread->replies->each->delete();
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
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

        if(static::whereSlug($slug)->exists()){
            $slug = $this->incrementSlug($slug);
        }

        $this->attributes['slug'] = $slug;
    }

    public function incrementSlug($slug)
    {
        $lastSlug = static::whereTitle($this->title)->latest('id')->value('slug');

        // php7 stuff, strings as arrays :D get the last character of $lastSlug (if 'test-thread-3' it returns '3')
        if(is_numeric($lastSlug[-1])) {
            return preg_replace_callback('/(\d+)$/', function($matches) {
                return $matches[1] + 1;
            }, $lastSlug);
        }
        return "{$slug}-2";
    }
}
