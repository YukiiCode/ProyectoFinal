<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Ingredient;

class ProductSeederNew extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Pizza Margherita',
                'description' => 'Salsa de tomate San Marzano, mozzarella fresca, albahaca y aceite de oliva virgen extra.',
                'price' => 12.00,
                'category' => 'Pizzas',
                'available' => true,
                'ingredients' => ['Harina de trigo', 'Tomate', 'Mozzarella', 'Albahaca', 'Aceite de oliva']
            ],
            [
                'name' => 'Pizza Pepperoni',
                'description' => 'Pizza clásica con pepperoni, mozzarella y salsa de tomate.',
                'price' => 14.00,
                'category' => 'Pizzas',
                'available' => true,
                'ingredients' => ['Harina de trigo', 'Tomate', 'Mozzarella', 'Pepperoni']
            ],
            [
                'name' => 'Pizza Quattro Stagioni',
                'description' => 'Cuatro estaciones con champiñones, jamón, alcachofas y aceitunas.',
                'price' => 16.00,
                'category' => 'Pizzas',
                'available' => true,
                'ingredients' => ['Harina de trigo', 'Tomate', 'Mozzarella', 'Champiñones']
            ],
            [
                'name' => 'Lasaña Clásica',
                'description' => 'Capas de pasta fresca, ragú de ternera, bechamel y queso parmesano.',
                'price' => 14.50,
                'category' => 'Pastas',
                'available' => true,
                'ingredients' => ['Pasta', 'Parmesano', 'Mantequilla', 'Ajo', 'Cebolla']
            ],
            [
                'name' => 'Spaghetti Carbonara',
                'description' => 'Pasta al dente con huevo, panceta, parmesano y pimienta negra.',
                'price' => 13.50,
                'category' => 'Pastas',
                'available' => true,
                'ingredients' => ['Pasta', 'Huevo', 'Parmesano', 'Mantequilla']
            ],
            [
                'name' => 'Fettuccine Alfredo',
                'description' => 'Pasta fresca con salsa de nata, mantequilla y parmesano.',
                'price' => 12.50,
                'category' => 'Pastas',
                'available' => true,
                'ingredients' => ['Pasta', 'Nata', 'Mantequilla', 'Parmesano']
            ],
            [
                'name' => 'Risotto ai Funghi Porcini',
                'description' => 'Arroz Carnaroli cremoso con setas porcini frescas y un toque de trufa.',
                'price' => 16.00,
                'category' => 'Arroces',
                'available' => true,
                'ingredients' => ['Champiñones', 'Parmesano', 'Mantequilla', 'Ajo']
            ],
            [
                'name' => 'Paella Valenciana',
                'description' => 'Arroz tradicional con pollo, verduras y azafrán.',
                'price' => 18.00,
                'category' => 'Arroces',
                'available' => true,
                'ingredients' => ['Pimientos', 'Ajo', 'Tomate']
            ],
            [
                'name' => 'Ensalada César',
                'description' => 'Lechuga romana, crutones, parmesano y salsa césar.',
                'price' => 9.50,
                'category' => 'Ensaladas',
                'available' => true,
                'ingredients' => ['Parmesano', 'Anchoas', 'Huevo']
            ],
            [
                'name' => 'Salmón a la Plancha',
                'description' => 'Filete de salmón fresco con verduras de temporada.',
                'price' => 19.50,
                'category' => 'Pescados',
                'available' => true,
                'ingredients' => ['Salmón', 'Aceite de oliva']
            ],
            [
                'name' => 'Gambas al Pil Pil',
                'description' => 'Gambas frescas salteadas con ajo y guindilla.',
                'price' => 15.50,
                'category' => 'Mariscos',
                'available' => true,
                'ingredients' => ['Gambas', 'Ajo', 'Aceite de oliva']
            ],
            [
                'name' => 'Tiramisú Casero',
                'description' => 'Bizcochos de soletilla bañados en café, crema de mascarpone y cacao.',
                'price' => 7.50,
                'category' => 'Postres',
                'available' => true,
                'ingredients' => ['Huevo', 'Nata']
            ],
            [
                'name' => 'Panna Cotta',
                'description' => 'Postre cremoso de nata con coulis de frutos rojos.',
                'price' => 6.50,
                'category' => 'Postres',
                'available' => true,
                'ingredients' => ['Nata', 'Almendras']
            ],
            [
                'name' => 'Gelato Artesanal',
                'description' => 'Helado artesanal de vainilla, chocolate o fresa.',
                'price' => 5.00,
                'category' => 'Postres',
                'available' => true,
                'ingredients' => ['Nata', 'Huevo']
            ]
        ];

        foreach ($products as $productData) {
            // Encontrar la categoría
            $category = ProductCategory::where('name', $productData['category'])->first();
            
            if (!$category) {
                continue; // Si no existe la categoría, saltar este producto
            }

            // Crear el producto
            $product = Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'category_id' => $category->id,
                'available' => $productData['available'],
            ]);            // Asociar ingredientes
            foreach ($productData['ingredients'] as $ingredientName) {
                $ingredient = Ingredient::where('name', $ingredientName)->first();
                if ($ingredient) {
                    $product->ingredients()->attach($ingredient->id, [
                        'quantity_required' => 1.0 // Cantidad por defecto
                    ]);
                }
            }
        }
    }
}
