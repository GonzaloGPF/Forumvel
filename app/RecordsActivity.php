<?php
/**
 * Created by PhpStorm.
 * User: Zalo
 * Date: 30/07/2017
 * Time: 18:46
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait RecordsActivity
{
    /**
     * Laravel will detect it's method name and will call it as it was a boot method from models which use this trait
     */
    protected static function bootRecordsActivity()
    {
        // All activities need a logged user
        if(Auth::guest()) return;

        foreach (self::getActivitiesToRecord() as $event){
            // Listen to model's events
            static::$event(function(Model $model){
                $model->recordActivity('created');
            });
        }

        static::deleting(function($model){
            $model->activity()->delete();
        });
    }

    /**
     * This array will be used by trait to register to Model's Events.
     * Every Model that uses this trait could override this method to let the trait listen to others events
     */
    public static function getActivitiesToRecord()
    {
        return ['created'];
    }

    public function recordActivity($event)
    {
        $this->activity()->create([
            'user_id' => Auth::id(),
            'type' => $this->getActivityType($event),
        ]);
    }

    public function getActivityType($event)
    {
        return $event . '_' . strtolower((new \ReflectionClass($this))->getShortName());
    }

    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}