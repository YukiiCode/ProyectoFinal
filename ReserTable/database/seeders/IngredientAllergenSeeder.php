<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\Allergen;
use App\Models\ProductIngredient;
use App\Models\IngredientAllergen;

class IngredientAllergenSeeder extends Seeder
{
    public function run()
    {
        // Crear ingredientes básicos
        $ingredientes = [
            ['name' => 'Harina de Trigo', 'unit_of_measure' => 'kg'],
            ['name' => 'Tomate', 'unit_of_measure' => 'kg'],
            ['name' => 'Mozzarella', 'unit_of_measure' => 'kg'],
            ['name' => 'Albahaca', 'unit_of_measure' => 'g'],
            ['name' => 'Aceite de Oliva', 'unit_of_measure' => 'l'],
            ['name' => 'Huevos', 'unit_of_measure' => 'unidades'],
            ['name' => 'Panceta', 'unit_of_measure' => 'kg'],
            ['name' => 'Parmesano', 'unit_of_measure' => 'kg'],
            ['name' => 'Pimienta Negra', 'unit_of_measure' => 'g'],
            ['name' => 'Mantequilla', 'unit_of_measure' => 'kg'],
            ['name' => 'Nata', 'unit_of_measure' => 'l'],
            ['name' => 'Carne de Ternera', 'unit_of_measure' => 'kg'],
            ['name' => 'Cebolla', 'unit_of_measure' => 'kg'],
            ['name' => 'Zanahoria', 'unit_of_measure' => 'kg'],
            ['name' => 'Apio', 'unit_of_measure' => 'kg'],
            ['name' => 'Vino Tinto', 'unit_of_measure' => 'l'],
            ['name' => 'Champiñones', 'unit_of_measure' => 'kg'],
            ['name' => 'Jamón Serrano', 'unit_of_measure' => 'kg'],
            ['name' => 'Aceitunas', 'unit_of_measure' => 'kg'],
            ['name' => 'Alcachofas', 'unit_of_measure' => 'kg'],
            ['name' => 'Lubina', 'unit_of_measure' => 'kg'],
            ['name' => 'Sal Marina', 'unit_of_measure' => 'kg'],
            ['name' => 'Limón', 'unit_of_measure' => 'kg'],
            ['name' => 'Mejillones', 'unit_of_measure' => 'kg'],
            ['name' => 'Almejas', 'unit_of_measure' => 'kg'],
            ['name' => 'Gambas', 'unit_of_measure' => 'kg'],
            ['name' => 'Calamares', 'unit_of_measure' => 'kg'],
            ['name' => 'Arroz Arborio', 'unit_of_measure' => 'kg'],
            ['name' => 'Mascarpone', 'unit_of_measure' => 'kg'],
            ['name' => 'Café', 'unit_of_measure' => 'kg'],
            ['name' => 'Cacao', 'unit_of_measure' => 'kg'],
            ['name' => 'Azúcar', 'unit_of_measure' => 'kg'],
            ['name' => 'Ricotta', 'unit_of_measure' => 'kg'],
            ['name' => 'Pistachos', 'unit_of_measure' => 'kg'],
            ['name' => 'Gelatina', 'unit_of_measure' => 'g'],
            ['name' => 'Frutos Rojos', 'unit_of_measure' => 'kg'],
            ['name' => 'Vainilla', 'unit_of_measure' => 'g'],
        ];

        foreach ($ingredientes as $ingrediente) {
            Ingredient::firstOrCreate(['name' => $ingrediente['name']], $ingrediente);
        }        // Obtener IDs de alérgenos
        $gluten = Allergen::where('name', 'Gluten')->first();
        $lacteos = Allergen::where('name', 'Lácteos')->first();
        $huevos = Allergen::where('name', 'Huevos')->first();
        $frutos_secos = Allergen::where('name', 'Frutos Secos')->first();
        $mariscos = Allergen::where('name', 'Mariscos')->first();
        $pescado = Allergen::where('name', 'Pescado')->first();
        $apio = Allergen::where('name', 'Apio')->first();

        // Verificar que todos los alérgenos existen
        if (!$gluten || !$lacteos || !$huevos || !$frutos_secos || !$mariscos || !$pescado || !$apio) {
            $this->command->error('Algunos alérgenos no fueron encontrados en la base de datos.');
            return;
        }

        // Relacionar ingredientes con alérgenos
        $relaciones_alergenos = [
            'Harina de Trigo' => [$gluten->id],
            'Mozzarella' => [$lacteos->id],
            'Huevos' => [$huevos->id],
            'Parmesano' => [$lacteos->id],
            'Mantequilla' => [$lacteos->id],
            'Nata' => [$lacteos->id],
            'Apio' => [$apio->id],
            'Mejillones' => [$mariscos->id],
            'Almejas' => [$mariscos->id],
            'Gambas' => [$mariscos->id],
            'Calamares' => [$mariscos->id],
            'Lubina' => [$pescado->id],
            'Mascarpone' => [$lacteos->id],
            'Ricotta' => [$lacteos->id],
            'Pistachos' => [$frutos_secos->id],
        ];

        foreach ($relaciones_alergenos as $ingrediente_nombre => $alergeno_ids) {
            $ingrediente = Ingredient::where('name', $ingrediente_nombre)->first();
            if ($ingrediente) {
                foreach ($alergeno_ids as $alergeno_id) {
                    IngredientAllergen::firstOrCreate([
                        'ingredient_id' => $ingrediente->id,
                        'allergen_id' => $alergeno_id
                    ]);
                }
            }
        }

        // Relacionar productos con ingredientes
        $productos_ingredientes = [
            'Pizza Margherita' => [
                'Harina de Trigo' => 0.3,
                'Tomate' => 0.1,
                'Mozzarella' => 0.15,
                'Albahaca' => 0.005,
                'Aceite de Oliva' => 0.02,
            ],
            'Spaghetti Carbonara' => [
                'Harina de Trigo' => 0.1, // para la pasta
                'Huevos' => 2,
                'Panceta' => 0.1,
                'Parmesano' => 0.05,
                'Pimienta Negra' => 0.002,
            ],
            'Fettuccine Alfredo' => [
                'Harina de Trigo' => 0.1,
                'Mantequilla' => 0.05,
                'Nata' => 0.1,
                'Parmesano' => 0.06,
            ],
            'Bruschetta Tricolore' => [
                'Harina de Trigo' => 0.1,
                'Tomate' => 0.15,
                'Mozzarella' => 0.08,
                'Albahaca' => 0.005,
                'Aceite de Oliva' => 0.015,
            ],
            'Risotto ai Frutti di Mare' => [
                'Arroz Arborio' => 0.15,
                'Mejillones' => 0.1,
                'Almejas' => 0.08,
                'Gambas' => 0.08,
                'Calamares' => 0.06,
                'Aceite de Oliva' => 0.02,
            ],
            'Tiramisù della Casa' => [
                'Mascarpone' => 0.25,
                'Huevos' => 3,
                'Café' => 0.05,
                'Cacao' => 0.02,
                'Azúcar' => 0.08,
            ],
            'Branzino in Crosta di Sale' => [
                'Lubina' => 0.4,
                'Sal Marina' => 0.2,
                'Limón' => 0.05,
                'Aceite de Oliva' => 0.02,
            ],
            'Cannoli Siciliani' => [
                'Harina de Trigo' => 0.1,
                'Ricotta' => 0.15,
                'Pistachos' => 0.03,
                'Azúcar' => 0.05,
                'Huevos' => 1,
            ],
            'Panna Cotta ai Frutti Rossi' => [
                'Nata' => 0.2,
                'Azúcar' => 0.06,
                'Gelatina' => 0.008,
                'Frutos Rojos' => 0.1,
                'Vainilla' => 0.002,
            ],
        ];

        foreach ($productos_ingredientes as $producto_nombre => $ingredientes) {
            $producto = Product::where('name', $producto_nombre)->first();
            if ($producto) {
                foreach ($ingredientes as $ingrediente_nombre => $cantidad) {
                    $ingrediente = Ingredient::where('name', $ingrediente_nombre)->first();
                    if ($ingrediente) {
                        ProductIngredient::firstOrCreate([
                            'product_id' => $producto->id,
                            'ingredient_id' => $ingrediente->id,
                        ], [
                            'quantity_required' => $cantidad
                        ]);
                    }
                }
            }
        }

        $this->command->info('Ingredientes y alérgenos relacionados correctamente!');
    }
}
