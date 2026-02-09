<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Deal;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $deals = Deal::all();

        foreach ($deals as $deal) {
            // Add a few activities for each deal
            Activity::create([
                'type' => 'note',
                'subject' => 'Initial contact',
                'description' => 'Spoke with the client about their requirements.',
                'activity_date' => now()->subDays(5),
                'status' => 'completed',
                'activityable_type' => Deal::class,
                'activityable_id' => $deal->id,
            ]);

            Activity::create([
                'type' => 'call',
                'subject' => 'Follow up call',
                'description' => 'Discussed pricing and timeline.',
                'activity_date' => now()->subDays(2),
                'status' => 'completed',
                'activityable_type' => Deal::class,
                'activityable_id' => $deal->id,
            ]);

            Activity::create([
                'type' => 'meeting',
                'subject' => 'Product Demo',
                'description' => 'Scheduled a demo for next week.',
                'activity_date' => now()->addDays(3),
                'status' => 'pending',
                'activityable_type' => Deal::class,
                'activityable_id' => $deal->id,
            ]);
        }
    }
}
