<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'pontian@npontu.com'],
            [
                'name' => 'Pontian Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password12#'),
                'email_verified_at' => now(),
            ]
        );

        // Seed Lookups
        $this->call([
            LeadSeeder::class,
            DealSeeder::class,
            WorkflowSeeder::class,
            CRMFullSeeder::class,
        ]);
    }
}
