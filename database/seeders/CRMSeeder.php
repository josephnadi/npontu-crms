<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Seeder;

class CRMSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $clients = [
            [
                'name' => 'Tech Corp',
                'industry' => 'Technology',
                'website' => 'https://techcorp.com',
                'phone' => '123-456-7890',
                'owner_id' => $user->id,
            ],
            [
                'name' => 'Marketing Pro',
                'industry' => 'Marketing',
                'website' => 'https://marketingpro.com',
                'phone' => '987-654-3210',
                'owner_id' => $user->id,
            ],
        ];

        foreach ($clients as $clientData) {
            $client = Client::create($clientData);

            // Add some contacts for each client
            Contact::create([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@' . strtolower(str_replace(' ', '', $clientData['name'])) . '.com',
                'job_title' => 'Manager',
                'client_id' => $client->id,
                'owner_id' => $user->id,
            ]);

            Contact::create([
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane@' . strtolower(str_replace(' ', '', $clientData['name'])) . '.com',
                'job_title' => 'Director',
                'client_id' => $client->id,
                'owner_id' => $user->id,
            ]);
        }
    }
}
