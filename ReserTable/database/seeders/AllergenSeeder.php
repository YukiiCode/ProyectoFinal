<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Allergen;

class AllergenSeeder extends Seeder
{
    public function run(): void
    {
        $allergens = [
            [
                'name' => 'Gluten',
                'icon' => 'wheat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lactosa',
                'icon' => 'glass-water',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Huevos',
                'icon' => 'egg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Frutos Secos',
                'icon' => 'seedling',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mariscos',
                'icon' => 'fish',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Soja',
                'icon' => 'leaf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Apio',
                'icon' => 'carrot',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mostaza',
                'icon' => 'circle',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($allergens as $allergen) {
            Allergen::firstOrCreate(
                ['name' => $allergen['name']], 
                $allergen
            );
        }
    }
}
