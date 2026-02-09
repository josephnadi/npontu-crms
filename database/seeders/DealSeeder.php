<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed default stages if they don't exist
        $defaultStages = [
            ['name' => 'Qualification', 'order_column' => 1, 'probability' => 20, 'color' => 'info'],
            ['name' => 'Proposal', 'order_column' => 2, 'probability' => 40, 'color' => 'primary'],
            ['name' => 'Negotiation', 'order_column' => 3, 'probability' => 60, 'color' => 'warning'],
            ['name' => 'Closing', 'order_column' => 4, 'probability' => 80, 'color' => 'success'],
            ['name' => 'Won', 'order_column' => 5, 'probability' => 100, 'color' => 'success'],
            ['name' => 'Lost', 'order_column' => 6, 'probability' => 0, 'color' => 'danger'],
        ];

        foreach ($defaultStages as $stageData) {
            \App\Models\DealStage::updateOrCreate(
                ['name' => $stageData['name']],
                $stageData
            );
        }

        $stages = \App\Models\DealStage::all();
        $user = \App\Models\User::first();

        if (!$user) {
            $user = \App\Models\User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]);
        }

        $deals = [
            [
                'title' => 'Enterprise License - TechCorp',
                'value' => 50000,
                'contact_name' => 'John Doe',
                'client_name' => 'TechCorp Solutions',
                'probability' => 20,
                'stage_name' => 'Qualification',
            ],
            [
                'title' => 'SaaS Subscription - StartupInc',
                'value' => 12000,
                'contact_name' => 'Jane Smith',
                'client_name' => 'StartupInc',
                'probability' => 40,
                'stage_name' => 'Proposal',
            ],
            [
                'title' => 'Consulting Project - GlobalBank',
                'value' => 75000,
                'contact_name' => 'Robert Brown',
                'client_name' => 'GlobalBank',
                'probability' => 60,
                'stage_name' => 'Negotiation',
            ],
            [
                'title' => 'Mobile App Dev - RetailCo',
                'value' => 35000,
                'contact_name' => 'Emily White',
                'client_name' => 'RetailCo',
                'probability' => 80,
                'stage_name' => 'Closing',
            ],
            [
                'title' => 'Annual Support - EduTech',
                'value' => 5000,
                'contact_name' => 'Michael Green',
                'client_name' => 'EduTech',
                'probability' => 100,
                'stage_name' => 'Won',
            ],
        ];

        foreach ($deals as $dealData) {
            $stage = $stages->where('name', $dealData['stage_name'])->first();
            
            \App\Models\Deal::create([
                'title' => $dealData['title'],
                'value' => $dealData['value'],
                'contact_name' => $dealData['contact_name'],
                'client_name' => $dealData['client_name'],
                'probability' => $dealData['probability'],
                'deal_stage_id' => $stage->id,
                'owner_id' => $user->id,
                'created_by' => $user->id,
                'expected_close_date' => now()->addDays(rand(10, 60)),
            ]);
        }
    }
}
