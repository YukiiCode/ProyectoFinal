<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use App\Models\Allergen;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            [
                'name' => 'Harina de trigo',
                'unit_of_measure' => 'kg',
                'allergens' => ['Gluten']
            ],
            [
                'name' => 'Tomate',
                'unit_of_measure' => 'kg',
                'allergens' => []
            ],
            [
                'name' => 'Mozzarella',
                'unit_of_measure' => 'kg',
                'allergens' => ['Lactosa']
            ],
            [
                'name' => 'Albahaca',
                'unit_of_measure' => 'g',
                'allergens' => []
            ],
            [
                'name' => 'Aceite de oliva',
                'unit_of_measure' => 'l',
                'allergens' => []
            ],
            [
                'name' => 'Huevo',
                'unit_of_measure' => 'unidad',
                'allergens' => ['Huevos']
            ],
            [
                'name' => 'Parmesano',
                'unit_of_measure' => 'kg',
                'allergens' => ['Lactosa']
            ],
            [
                'name' => 'Pasta',
                'unit_of_measure' => 'kg',
                'allergens' => ['Gluten', 'Huevos']
            ],
            [
                'name' => 'Salmón',
                'unit_of_measure' => 'kg',
                'allergens' => ['Pescado']
            ],
            [
                'name' => 'Gambas',
                'unit_of_measure' => 'kg',
                'allergens' => ['Crustáceos']
            ],
            [
                'name' => 'Almendras',
                'unit_of_measure' => 'kg',
                'allergens' => ['Frutos secos']
            ],
            [
                'name' => 'Mantequilla',
                'unit_of_measure' => 'kg',
                'allergens' => ['Lactosa']
            ],
            [
                'name' => 'Nata',
                'unit_of_measure' => 'l',
                'allergens' => ['Lactosa']
            ],
            [
                'name' => 'Ajo',
                'unit_of_measure' => 'kg',
                'allergens' => []
            ],
            [
                'name' => 'Cebolla',
                'unit_of_measure' => 'kg',
                'allergens' => []
            ],
            [
                'name' => 'Pepperoni',
                'unit_of_measure' => 'kg',
                'allergens' => []
            ],
            [
                'name' => 'Champiñones',
                'unit_of_measure' => 'kg',
                'allergens' => []
            ],
            [
                'name' => 'Pimientos',
                'unit_of_measure' => 'kg',
                'allergens' => []
            ],
            [
                'name' => 'Atún',
                'unit_of_measure' => 'kg',
                'allergens' => ['Pescado']
            ],
            [
                'name' => 'Anchoas',
                'unit_of_measure' => 'kg',
                'allergens' => ['Pescado']
            ]
        ];

        foreach ($ingredients as $ingredientData) {
            $ingredient = Ingredient::create([
                'name' => $ingredientData['name'],
                'unit_of_measure' => $ingredientData['unit_of_measure']
            ]);

            // Asociar alérgenos
            foreach ($ingredientData['allergens'] as $allergenName) {
                $allergen = Allergen::where('name', $allergenName)->first();
                if ($allergen) {
                    $ingredient->allergens()->attach($allergen->id);
                }
            }
        }
    }
}
