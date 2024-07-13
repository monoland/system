<?php

namespace Module\System\Traits;

use Illuminate\Database\Eloquent\Model;
use Module\System\Models\SystemUserLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    /**
     * The "booted" method of the model.
     */
    public static function bootAuditable(): void
    {
        static::approved(function (Model $model) {
            SystemUserLog::eventLog('approved', $model);
        });

        static::confirmed(function (Model $model) {
            SystemUserLog::eventLog('confirmed', $model);
        });

        static::created(function (Model $model) {
            SystemUserLog::eventLog('created', $model);
        });

        static::deleted(function (Model $model) {
            if ($model->forceDeleting) {
                SystemUserLog::eventLog('deleted', $model);
            } else {
                SystemUserLog::eventLog('trashed', $model);
            }
        });

        static::printed(function (Model $model) {
            SystemUserLog::eventLog('printed', $model);
        });

        static::published(function (Model $model) {
            SystemUserLog::eventLog('published', $model);
        });

        static::rejected(function (Model $model) {
            SystemUserLog::eventLog('rejected', $model);
        });

        static::restored(function (Model $model) {
            SystemUserLog::eventLog('restored', $model);
        });

        static::signed(function (Model $model) {
            SystemUserLog::eventLog('signed', $model);
        });

        static::submitted(function (Model $model) {
            SystemUserLog::eventLog('submitted', $model);
        });

        static::synced(function (Model $model) {
            SystemUserLog::eventLog('synced', $model);
        });

        static::updated(function (Model $model) {
            SystemUserLog::eventLog('updated', $model);
        });

        static::verified(function (Model $model) {
            SystemUserLog::eventLog('verified', $model);
        });
    }

    /**
     * Register a approved model event with the dispatcher.
     *
     * @param  \Illuminate\Events\QueuedClosure|\Closure|string  $callback
     * @return void
     */
    public static function approved($callback)
    {
        static::registerModelEvent('approved', $callback);
    }

    /**
     * Register a confirmed model event with the dispatcher.
     *
     * @param  \Illuminate\Events\QueuedClosure|\Closure|string  $callback
     * @return void
     */
    public static function confirmed($callback)
    {
        static::registerModelEvent('confirmed', $callback);
    }

    /**
     * Register a printed model event with the dispatcher.
     *
     * @param  \Illuminate\Events\QueuedClosure|\Closure|string  $callback
     * @return void
     */
    public static function printed($callback)
    {
        static::registerModelEvent('printed', $callback);
    }


    /**
     * Register a published model event with the dispatcher.
     *
     * @param  \Illuminate\Events\QueuedClosure|\Closure|string  $callback
     * @return void
     */
    public static function published($callback)
    {
        static::registerModelEvent('published', $callback);
    }

    /**
     * Register a rejected model event with the dispatcher.
     *
     * @param  \Illuminate\Events\QueuedClosure|\Closure|string  $callback
     * @return void
     */
    public static function rejected($callback)
    {
        static::registerModelEvent('rejected', $callback);
    }

    /**
     * Register a signed model event with the dispatcher.
     *
     * @param  \Illuminate\Events\QueuedClosure|\Closure|string  $callback
     * @return void
     */
    public static function signed($callback)
    {
        static::registerModelEvent('signed', $callback);
    }

    /**
     * Register a submitted model event with the dispatcher.
     *
     * @param  \Illuminate\Events\QueuedClosure|\Closure|string  $callback
     * @return void
     */
    public static function submitted($callback)
    {
        static::registerModelEvent('submitted', $callback);
    }


    /**
     * Register a synced model event with the dispatcher.
     *
     * @param  \Illuminate\Events\QueuedClosure|\Closure|string  $callback
     * @return void
     */
    public static function synced($callback)
    {
        static::registerModelEvent('synced', $callback);
    }

    /**
     * Register a verified model event with the dispatcher.
     *
     * @param  \Illuminate\Events\QueuedClosure|\Closure|string  $callback
     * @return void
     */
    public static function verified($callback)
    {
        static::registerModelEvent('verified', $callback);
    }

    /**
     * The activitylogs function
     *
     * @return MorphMany
     */
    public function activitylogs(): MorphMany
    {
        return $this->morphMany(SystemUserLog::class, 'subjectable');
    }
}
