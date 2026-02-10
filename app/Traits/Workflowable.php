<?php

namespace App\Traits;

use App\Services\CRM\WorkflowEngineService;
use Illuminate\Database\Eloquent\Model;

trait Workflowable
{
    public static function bootWorkflowable()
    {
        static::created(function (Model $model) {
            app(WorkflowEngineService::class)->dispatch(
                strtolower(class_basename($model)) . '.created',
                $model
            );
        });

        static::updated(function (Model $model) {
            app(WorkflowEngineService::class)->dispatch(
                strtolower(class_basename($model)) . '.updated',
                $model
            );
        });
    }

    /**
     * Relationship to tasks (assumed polymorphic or direct).
     */
    public function tasks()
    {
        return $this->morphMany(\App\Models\Task::class, 'taskable');
    }
}
