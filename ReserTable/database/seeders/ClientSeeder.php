<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'name' => 'María García',
                'email' => 'maria.garcia@example.com',
                'phone' => '+34 600 123 456',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Carlos López',
                'email' => 'carlos.lopez@example.com',
                'phone' => '+34 600 234 567',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ana Martínez',
                'email' => 'ana.martinez@example.com',
                'phone' => '+34 600 345 678',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Pedro Ruiz',
                'email' => 'pedro.ruiz@example.com',
                'phone' => '+34 600 456 789',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Laura Sánchez',
                'email' => 'laura.sanchez@example.com',
                'phone' => '+34 600 567 890',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Miguel Fernández',
                'email' => 'miguel.fernandez@example.com',
                'phone' => '+34 600 678 901',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }
    }
}
