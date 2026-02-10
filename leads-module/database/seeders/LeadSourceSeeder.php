<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            ['name' => 'Website Form', 'description' => 'Lead submitted through website contact form', 'is_active' => true],
            ['name' => 'Referral', 'description' => 'Lead referred by existing customer or partner', 'is_active' => true],
            ['name' => 'Trade Show', 'description' => 'Lead met at trade show or exhibition', 'is_active' => true],
            ['name' => 'Cold Call', 'description' => 'Lead acquired through cold calling', 'is_active' => true],
            ['name' => 'Social Media', 'description' => 'Lead from social media channels', 'is_active' => true],
            ['name' => 'Email Campaign', 'description' => 'Lead from email marketing campaign', 'is_active' => true],
            ['name' => 'Partner', 'description' => 'Lead from business partner', 'is_active' => true],
            ['name' => 'Event', 'description' => 'Lead from event or conference', 'is_active' => true],
            ['name' => 'Other', 'description' => 'Other lead source', 'is_active' => true],
        ];

        foreach ($sources as $source) {
            DB::table('lead_sources')->updateOrInsert(
                ['name' => $source['name']],
                array_merge($source, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
