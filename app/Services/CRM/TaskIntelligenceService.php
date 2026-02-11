<?php

namespace App\Services\CRM;

use App\Models\Task;
use Carbon\Carbon;

class TaskIntelligenceService
{
    /**
     * Calculate priority score for a task.
     */
    public function calculatePriorityScore(Task $task): int
    {
        $score = 0;

        // Base score by priority
        switch ($task->priority) {
            case 'high': $score += 40; break;
            case 'medium': $score += 20; break;
            case 'low': $score += 5; break;
        }

        // Deadline proximity
        if ($task->due_date) {
            $daysUntilDue = now()->diffInDays($task->due_date, false);
            if ($daysUntilDue < 0) {
                $score += 50; // Overdue
            } elseif ($daysUntilDue <= 1) {
                $score += 30; // Due tomorrow
            } elseif ($daysUntilDue <= 3) {
                $score += 15; // Due soon
            }
        }

        // SLA Proximity
        if ($task->sla_minutes && $task->created_at) {
            $slaDeadline = $task->created_at->addMinutes($task->sla_minutes);
            $minutesRemaining = now()->diffInMinutes($slaDeadline, false);
            
            if ($minutesRemaining < 0) {
                $score += 40; // SLA Breached
            } elseif ($minutesRemaining < 30) {
                $score += 25; // SLA about to breach
            }
        }

        // Relation importance (Lead vs Deal vs Client)
        if ($task->taskable_type === 'App\Models\Deal') {
            $score += 15; // Deals are high value
        }

        return min(100, $score);
    }

    /**
     * Suggest next actions for a task or model.
     */
    public function suggestNextActions(Task $task): array
    {
        $suggestions = [];

        if ($task->status !== 'completed') {
            if ($task->priority === 'high' && !$task->escalated_at) {
                $suggestions[] = [
                    'type' => 'escalate',
                    'label' => 'Escalate to Manager',
                    'description' => 'This high priority task is nearing SLA breach.'
                ];
            }

            if ($task->taskable_type === 'App\Models\Lead') {
                $suggestions[] = [
                    'type' => 'call',
                    'label' => 'Schedule Discovery Call',
                    'description' => 'Suggested follow-up for new lead.'
                ];
            }
        }

        return $suggestions;
    }
}
