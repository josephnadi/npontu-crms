<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\User;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]);
        }

        // Ensure lead sources exist
        $sources = [
            ['name' => 'Website', 'description' => 'Leads from website contact form'],
            ['name' => 'LinkedIn', 'description' => 'Leads from LinkedIn outreach'],
            ['name' => 'Referral', 'description' => 'Leads from word of mouth'],
            ['name' => 'Event', 'description' => 'Leads from conferences and events'],
            ['name' => 'Cold Call', 'description' => 'Leads from cold calling'],
        ];

        foreach ($sources as $source) {
            \App\Models\LeadSource::updateOrCreate(['name' => $source['name']], $source);
        }

        $sourceMap = \App\Models\LeadSource::pluck('id', 'name')->toArray();

        $leads = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@techcorp.com',
                'phone' => '+1 555-0101',
                'company_name' => 'TechCorp Solutions',
                'job_title' => 'Product Manager',
                'lead_source_id' => $sourceMap['Website'] ?? null,
                'status' => 'new',
                'score' => 85,
                'notes' => 'Interested in our enterprise plan.',
                'owner_id' => $user->id,
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@global-it.net',
                'phone' => '+1 555-0102',
                'company_name' => 'Global IT',
                'job_title' => 'CTO',
                'lead_source_id' => $sourceMap['LinkedIn'] ?? null,
                'status' => 'contacted',
                'score' => 60,
                'notes' => 'Discussed initial requirements on call.',
                'owner_id' => $user->id,
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'email' => 'm.brown@startup.io',
                'phone' => '+1 555-0103',
                'company_name' => 'Innovate AI',
                'job_title' => 'Founder',
                'lead_source_id' => $sourceMap['Referral'] ?? null,
                'status' => 'qualified',
                'score' => 95,
                'notes' => 'High potential for conversion. Budget approved.',
                'owner_id' => $user->id,
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Wilson',
                'email' => 'sarah.w@retail-pro.com',
                'phone' => '+1 555-0104',
                'company_name' => 'Retail Pro',
                'job_title' => 'Marketing Director',
                'lead_source_id' => $sourceMap['Event'] ?? null,
                'status' => 'new',
                'score' => 45,
                'notes' => 'Met at the SaaS conference.',
                'owner_id' => $user->id,
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Lee',
                'email' => 'david.lee@consultancy.biz',
                'phone' => '+1 555-0105',
                'company_name' => 'Lee Consulting',
                'job_title' => 'Principal Consultant',
                'lead_source_id' => $sourceMap['Cold Call'] ?? null,
                'status' => 'lost',
                'score' => 20,
                'notes' => 'No budget for this year.',
                'owner_id' => $user->id,
            ],
        ];

        foreach ($leads as $lead) {
            Lead::updateOrCreate(['email' => $lead['email']], $lead);
        }
    }
}
