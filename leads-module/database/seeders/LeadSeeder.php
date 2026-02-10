<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $sources = LeadSource::all();

        if (!$user || $sources->isEmpty()) {
            $this->command->warn('Please ensure users and lead sources exist before running this seeder.');
            return;
        }

        $leads = [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@techcorp.com',
                'phone' => '+1-555-0101',
                'mobile' => '+1-555-0102',
                'company_name' => 'TechCorp Inc',
                'job_title' => 'CTO',
                'lead_source_id' => $sources->where('name', 'Website Form')->first()->id,
                'status' => 'new',
                'score' => 45,
                'city' => 'San Francisco',
                'state' => 'CA',
                'country' => 'USA',
                'notes' => 'Interested in enterprise solution',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.j@innovate.io',
                'phone' => '+1-555-0201',
                'company_name' => 'Innovate Solutions',
                'job_title' => 'VP of Operations',
                'lead_source_id' => $sources->where('name', 'Referral')->first()->id,
                'status' => 'contacted',
                'score' => 65,
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'notes' => 'Had initial call, following up next week',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'email' => 'mchen@dataflow.com',
                'phone' => '+1-555-0301',
                'mobile' => '+1-555-0302',
                'company_name' => 'DataFlow Systems',
                'job_title' => 'CEO',
                'lead_source_id' => $sources->where('name', 'Trade Show')->first()->id,
                'status' => 'qualified',
                'score' => 85,
                'city' => 'Austin',
                'state' => 'TX',
                'country' => 'USA',
                'notes' => 'Ready to move forward, requesting proposal',
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Rodriguez',
                'email' => 'emily.r@cloudbase.net',
                'phone' => '+1-555-0401',
                'company_name' => 'CloudBase',
                'job_title' => 'Product Manager',
                'lead_source_id' => $sources->where('name', 'Social Media')->first()->id,
                'status' => 'qualified',
                'score' => 78,
                'city' => 'Seattle',
                'state' => 'WA',
                'country' => 'USA',
                'notes' => 'Budget approved for Q2',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Kim',
                'email' => 'david.kim@nextstep.com',
                'phone' => '+1-555-0501',
                'company_name' => 'NextStep Technologies',
                'job_title' => 'Director of IT',
                'lead_source_id' => $sources->where('name', 'Email Campaign')->first()->id,
                'status' => 'converted',
                'score' => 95,
                'city' => 'Boston',
                'state' => 'MA',
                'country' => 'USA',
                'notes' => 'Successfully converted to customer',
            ],
            [
                'first_name' => 'Lisa',
                'last_name' => 'Anderson',
                'email' => 'lisa.a@startup.io',
                'phone' => '+1-555-0601',
                'company_name' => 'Startup Ventures',
                'job_title' => 'Founder',
                'lead_source_id' => $sources->where('name', 'Event')->first()->id,
                'status' => 'lost',
                'score' => 30,
                'city' => 'Los Angeles',
                'state' => 'CA',
                'country' => 'USA',
                'notes' => 'Went with competitor',
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Taylor',
                'email' => 'robert.taylor@fintech.com',
                'phone' => '+1-555-0701',
                'mobile' => '+1-555-0702',
                'company_name' => 'FinTech Global',
                'job_title' => 'CFO',
                'lead_source_id' => $sources->where('name', 'Partner')->first()->id,
                'status' => 'contacted',
                'score' => 55,
                'city' => 'Chicago',
                'state' => 'IL',
                'country' => 'USA',
                'notes' => 'Scheduled demo for next month',
            ],
            [
                'first_name' => 'Jennifer',
                'last_name' => 'Lee',
                'email' => 'jlee@ecommerce.shop',
                'phone' => '+1-555-0801',
                'company_name' => 'E-Commerce Plus',
                'job_title' => 'Marketing Director',
                'lead_source_id' => $sources->where('name', 'Cold Call')->first()->id,
                'status' => 'new',
                'score' => 40,
                'city' => 'Miami',
                'state' => 'FL',
                'country' => 'USA',
                'notes' => 'Initial contact made',
            ],
        ];

        foreach ($leads as $leadData) {
            Lead::create([
                ...$leadData,
                'owner_id' => $user->id,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        }

        $this->command->info('Created ' . count($leads) . ' sample leads.');
    }
}
