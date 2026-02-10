<?php

namespace App\Services\CRM;

use App\Models\Workflow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Services\CRM\TaskIntelligenceService;

class WorkflowEngineService
{
    /**
     * Dispatch an event to the workflow engine.
     */
    public function dispatch(string $eventType, Model $model): void
    {
        $workflows = Workflow::where('event_type', $eventType)
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($workflows as $workflow) {
            if ($this->evaluateConditions($workflow, $model)) {
                $this->executeActions($workflow, $model);
            }
        }
    }

    /**
     * Evaluate workflow conditions against the model.
     */
    protected function evaluateConditions(Workflow $workflow, Model $model): bool
    {
        $conditions = $workflow->conditions;
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? '==';
            $value = $condition['value'] ?? null;

            if (!$field) continue;

            $modelValue = $model->{$field};

            switch ($operator) {
                case '==': if ($modelValue != $value) return false; break;
                case '!=': if ($modelValue == $value) return false; break;
                case '>':  if ($modelValue <= $value) return false; break;
                case '<':  if ($modelValue >= $value) return false; break;
                case 'contains': if (stripos($modelValue, $value) === false) return false; break;
                case 'in': if (!in_array($modelValue, (array)$value)) return false; break;
            }
        }

        return true;
    }

    /**
     * Execute workflow actions.
     */
    protected function executeActions(Workflow $workflow, Model $model): void
    {
        $actions = $workflow->actions;
        if (empty($actions)) {
            return;
        }

        foreach ($actions as $action) {
            $type = $action['type'] ?? null;
            $params = $action['params'] ?? [];

            switch ($type) {
                case 'update_field':
                    $model->update([$params['field'] => $params['value']]);
                    break;
                case 'create_task':
                    $task = $model->tasks()->create([
                        'title' => $params['title'] ?? 'Auto-generated Task',
                        'description' => $params['description'] ?? '',
                        'due_date' => now()->addDays($params['days_due'] ?? 1),
                        'status' => 'pending',
                        'owner_id' => $model->owner_id ?? null,
                        'priority' => $params['priority'] ?? 'medium',
                    ]);

                    // Use TaskIntelligenceService to refine the task if needed
                    if ($task) {
                        $intelligence = app(TaskIntelligenceService::class);
                        $score = $intelligence->calculatePriorityScore($task);
                        $task->updateQuietly(['priority_score' => $score]);
                    }
                    break;
                case 'send_notification':
                    // Implement notification logic
                    Log::info("Workflow notification: " . ($params['message'] ?? ''));
                    break;
                case 'log_communication':
                    if (method_exists($model, 'communications')) {
                        $model->communications()->create([
                            'type' => $params['type'] ?? 'email',
                            'direction' => $params['direction'] ?? 'outbound',
                            'subject' => $params['subject'] ?? 'Automated Message',
                            'content' => $params['content'] ?? '',
                            'from_identifier' => $params['from'] ?? 'system@crm.com',
                            'to_identifier' => $model->email ?? $model->phone ?? 'unknown',
                            'status' => 'sent',
                        ]);
                    }
                    
                    // Also log as an Activity if applicable
                    if (method_exists($model, 'activities')) {
                        $model->activities()->create([
                            'type' => $params['type'] ?? 'email',
                            'subject' => $params['subject'] ?? 'Automated Message',
                            'description' => $params['content'] ?? '',
                            'activity_date' => now(),
                            'status' => 'completed',
                            'owner_id' => $model->owner_id ?? null,
                        ]);
                    }
                    break;
                case 'create_activity':
                    if (method_exists($model, 'activities')) {
                        $model->activities()->create([
                            'type' => $params['type'] ?? 'task',
                            'subject' => $params['subject'] ?? 'Automated Activity',
                            'description' => $params['description'] ?? '',
                            'activity_date' => now()->addDays($params['days_due'] ?? 0),
                            'due_date' => $params['due_date'] ?? null,
                            'status' => 'pending',
                            'owner_id' => $model->owner_id ?? null,
                        ]);
                    }
                    break;
            }
        }
    }
}
