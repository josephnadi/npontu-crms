<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Engagement;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Partner;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class CRMFullSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        // 1. Create Lead Sources
        $sources = ['Website', 'Referral', 'Cold Call', 'LinkedIn', 'Partner', 'Trade Show'];
        foreach ($sources as $sourceName) {
            LeadSource::firstOrCreate(['name' => $sourceName]);
        }

        // 2. Create Deal Stages
        $stages = [
            ['name' => 'Discovery', 'probability' => 10, 'order_column' => 1],
            ['name' => 'Qualification', 'probability' => 20, 'order_column' => 2],
            ['name' => 'Proposal', 'probability' => 50, 'order_column' => 3],
            ['name' => 'Negotiation', 'probability' => 80, 'order_column' => 4],
            ['name' => 'Closed Won', 'probability' => 100, 'order_column' => 5],
            ['name' => 'Closed Lost', 'probability' => 0, 'order_column' => 6],
        ];
        foreach ($stages as $stageData) {
            DealStage::firstOrCreate(['name' => $stageData['name']], $stageData);
        }

        // 3. Create Leads
        Lead::factory()->count(10)->create([
            'owner_id' => $user->id,
            'status' => 'new'
        ]);

        Lead::factory()->count(5)->create([
            'owner_id' => $user->id,
            'status' => 'qualified'
        ]);

        // 4. Create Partners
        $partners = Partner::factory()->count(5)->create([
            'user_id' => $user->id
        ]);

        // 5. Create Clients with Contacts, Deals, and Tickets
        Client::factory()->count(10)->create([
            'owner_id' => $user->id
        ])->each(function ($client) use ($user, $partners) {
            // Add contacts
            $contacts = Contact::factory()->count(rand(1, 3))->create([
                'client_id' => $client->id,
                'owner_id' => $user->id
            ]);

            // Add deals
            Deal::factory()->count(rand(1, 2))->create([
                'client_id' => $client->id,
                'contact_id' => $contacts->random()->id,
                'owner_id' => $user->id,
                'partner_id' => rand(0, 1) ? $partners->random()->id : null
            ]);

            // Add tickets
            Ticket::factory()->count(rand(0, 2))->create([
                'client_id' => $client->id,
                'contact_id' => $contacts->random()->id,
                'user_id' => $user->id
            ]);

            // Add engagements
            Engagement::factory()->count(rand(2, 5))->create([
                'engageable_id' => $client->id,
                'engageable_type' => Client::class,
                'user_id' => $user->id
            ]);

            foreach ($contacts as $contact) {
                Engagement::factory()->count(rand(1, 3))->create([
                    'engageable_id' => $contact->id,
                    'engageable_type' => Contact::class,
                    'user_id' => $user->id
                ]);
            }
        });

        // 6. Create some Activities for Leads and Deals
        $leads = Lead::all();
        $deals = Deal::all();

        foreach ($leads->random(min(5, $leads->count())) as $lead) {
            Activity::factory()->count(rand(1, 3))->create([
                'owner_id' => $user->id,
                'activityable_type' => Lead::class,
                'activityable_id' => $lead->id,
            ]);
        }

        foreach ($deals->random(min(5, $deals->count())) as $deal) {
            Activity::factory()->count(rand(1, 3))->create([
                'owner_id' => $user->id,
                'activityable_type' => Deal::class,
                'activityable_id' => $deal->id,
            ]);
        }
    }
}
