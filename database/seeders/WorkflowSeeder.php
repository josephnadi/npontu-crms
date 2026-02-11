<?php

namespace Database\Seeders;

use App\Models\Workflow;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. High Potential Lead Workflow
        Workflow::create([
            'name' => 'High Potential Lead Alert',
            'event_type' => 'lead.updated', // Triggered when score is calculated
            'conditions' => [
                ['field' => 'score', 'operator' => '>=', 'value' => 70],
                ['field' => 'status', 'operator' => '!=', 'value' => 'converted'],
            ],
            'actions' => [
                [
                    'type' => 'create_task',
                    'params' => [
                        'title' => 'Urgent: High Potential Lead Follow-up',
                        'priority' => 'high',
                        'sla_minutes' => 60, // 1 hour response time
                    ]
                ],
                [
                    'type' => 'update_field',
                    'params' => [
                        'field' => 'status',
                        'value' => 'qualified'
                    ]
                ]
            ],
            'is_active' => true,
        ]);

        // 2. New Lead Welcome Workflow
        Workflow::create([
            'name' => 'Auto-Log Welcome Email',
            'event_type' => 'lead.created',
            'conditions' => [
                ['field' => 'email', 'operator' => '!=', 'value' => null],
            ],
            'actions' => [
                [
                    'type' => 'log_communication',
                    'params' => [
                        'type' => 'email',
                        'direction' => 'outbound',
                        'subject' => 'Welcome to our platform',
                        'content' => 'Automated welcome email sent via workflow engine.'
                    ]
                ]
            ],
            'is_active' => true,
        ]);

        // 3. Deal Stage Escalation
        Workflow::create([
            'name' => 'High Value Deal Escalation',
            'event_type' => 'deal.updated',
            'conditions' => [
                ['field' => 'value', 'operator' => '>', 'value' => 10000],
                ['field' => 'status', 'operator' => '=', 'value' => 'open'],
            ],
            'actions' => [
                [
                    'type' => 'create_task',
                    'params' => [
                        'title' => 'Manager Review Required: High Value Deal',
                        'priority' => 'high',
                        'sla_minutes' => 120,
                    ]
                ]
            ],
            'is_active' => true,
        ]);
    }
}
