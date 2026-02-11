<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\CRM\TaskIntelligenceService;

class TaskObserver
{
    protected $intelligenceService;

    public function __construct(TaskIntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        $task->priority_score = $this->intelligenceService->calculatePriorityScore($task);
        $task->saveQuietly();
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        // Recalculate if relevant fields changed
        $relevantFields = ['priority', 'due_date', 'status', 'sla_minutes'];
        if ($task->wasChanged($relevantFields)) {
            $task->priority_score = $this->intelligenceService->calculatePriorityScore($task);
            $task->saveQuietly();
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
