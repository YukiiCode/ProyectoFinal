<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;

class ImprovedMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Crear categorías si no existen
        $categories = [
            ['name' => 'Entrantes', 'description' => 'Deliciosos aperitivos para comenzar'],
            ['name' => 'Pasta', 'description' => 'Pasta fresca casera'],
            ['name' => 'Pizza', 'description' => 'Pizzas al horno de leña'],
            ['name' => 'Carnes', 'description' => 'Cortes selectos y carnes premium'],
            ['name' => 'Pescados', 'description' => 'Pescados frescos del día'],
            ['name' => 'Postres', 'description' => 'Dulces tentaciones para finalizar'],
            ['name' => 'Bebidas', 'description' => 'Refrescos, vinos y cocteles']
        ];

        foreach ($categories as $categoryData) {
            ProductCategory::firstOrCreate(['name' => $categoryData['name']], $categoryData);
        }

        // Obtener las categorías
        $entrantes = ProductCategory::where('name', 'Entrantes')->first();
        $pasta = ProductCategory::where('name', 'Pasta')->first();
        $pizza = ProductCategory::where('name', 'Pizza')->first();
        $carnes = ProductCategory::where('name', 'Carnes')->first();
        $pescados = ProductCategory::where('name', 'Pescados')->first();
        $postres = ProductCategory::where('name', 'Postres')->first();
        $bebidas = ProductCategory::where('name', 'Bebidas')->first();

        // Productos mejorados con SVG
        $products = [
            // Entrantes
            [
                'name' => 'Bruschetta Italiana',
                'description' => 'Pan tostado con tomate fresco, albahaca y mozzarella di bufala',
                'price' => 8.50,
                'category_id' => $entrantes->id,
                'available' => true,
                'image_svg' => 'bruschetta'
            ],
            [
                'name' => 'Antipasto Misto',
                'description' => 'Selección de embutidos italianos, quesos y aceitunas marinadas',
                'price' => 12.90,
                'category_id' => $entrantes->id,
                'available' => true,
                'image_svg' => 'antipasto'
            ],
            [
                'name' => 'Carpaccio di Manzo',
                'description' => 'Finas láminas de ternera con rúcula, parmesano y aceite de trufa',
                'price' => 14.50,
                'category_id' => $entrantes->id,
                'available' => true,
                'image_svg' => 'carpaccio'
            ],

            // Pasta
            [
                'name' => 'Spaghetti Carbonara',
                'description' => 'Spaghetti con huevo, pancetta, pecorino y pimienta negra',
                'price' => 13.90,
                'category_id' => $pasta->id,
                'available' => true,
                'image_svg' => 'carbonara'
            ],
            [
                'name' => 'Fettuccine Alfredo',
                'description' => 'Fettuccine caseros con salsa cremosa de parmesano y mantequilla',
                'price' => 12.50,
                'category_id' => $pasta->id,
                'available' => true,
                'image_svg' => 'fettuccine'
            ],
            [
                'name' => 'Lasagna della Nonna',
                'description' => 'Lasaña tradicional con carne, bechamel y tres quesos',
                'price' => 15.90,
                'category_id' => $pasta->id,
                'available' => true,
                'image_svg' => 'lasagna'
            ],
            [
                'name' => 'Ravioli ai Funghi',
                'description' => 'Raviolis rellenos de setas con salsa de trufa blanca',
                'price' => 16.50,
                'category_id' => $pasta->id,
                'available' => true,
                'image_svg' => 'ravioli'
            ],

            // Pizza
            [
                'name' => 'Pizza Margherita',
                'description' => 'Salsa de tomate, mozzarella fresca y albahaca',
                'price' => 11.90,
                'category_id' => $pizza->id,
                'available' => true,
                'image_svg' => 'margherita'
            ],
            [
                'name' => 'Pizza Prosciutto',
                'description' => 'Salsa de tomate, mozzarella, jamón de Parma y rúcula',
                'price' => 14.90,
                'category_id' => $pizza->id,
                'available' => true,
                'image_svg' => 'prosciutto'
            ],
            [
                'name' => 'Pizza Quattro Stagioni',
                'description' => 'Cuatro estaciones: champiñones, alcachofas, jamón y aceitunas',
                'price' => 15.90,
                'category_id' => $pizza->id,
                'available' => true,
                'image_svg' => 'quattro_stagioni'
            ],
            [
                'name' => 'Pizza Diavola',
                'description' => 'Salsa de tomate, mozzarella, salami picante y chile',
                'price' => 13.90,
                'category_id' => $pizza->id,
                'available' => true,
                'image_svg' => 'diavola'
            ],

            // Carnes
            [
                'name' => 'Osso Buco Milanese',
                'description' => 'Jarrete de ternera estofado con verduras y gremolata',
                'price' => 22.90,
                'category_id' => $carnes->id,
                'available' => true,
                'image_svg' => 'osso_buco'
            ],
            [
                'name' => 'Bistecca alla Fiorentina',
                'description' => 'Chuletón de ternera a la parrilla con aceite de oliva y limón',
                'price' => 28.90,
                'category_id' => $carnes->id,
                'available' => true,
                'image_svg' => 'bistecca'
            ],
            [
                'name' => 'Saltimbocca alla Romana',
                'description' => 'Escalope de ternera con jamón de Parma y salvia',
                'price' => 19.90,
                'category_id' => $carnes->id,
                'available' => true,
                'image_svg' => 'saltimbocca'
            ],

            // Pescados
            [
                'name' => 'Salmone alla Griglia',
                'description' => 'Salmón a la parrilla con verduras mediterráneas',
                'price' => 18.90,
                'category_id' => $pescados->id,
                'available' => true,
                'image_svg' => 'salmon'
            ],
            [
                'name' => 'Branzino in Crosta',
                'description' => 'Lubina en costra de sal con hierbas aromáticas',
                'price' => 21.90,
                'category_id' => $pescados->id,
                'available' => true,
                'image_svg' => 'branzino'
            ],
            [
                'name' => 'Risotto ai Frutti di Mare',
                'description' => 'Risotto cremoso con mariscos frescos del día',
                'price' => 17.90,
                'category_id' => $pescados->id,
                'available' => true,
                'image_svg' => 'risotto_mare'
            ],

            // Postres
            [
                'name' => 'Tiramisu della Casa',
                'description' => 'El clásico postre italiano con café y mascarpone',
                'price' => 6.90,
                'category_id' => $postres->id,
                'available' => true,
                'image_svg' => 'tiramisu'
            ],
            [
                'name' => 'Panna Cotta ai Frutti Rossi',
                'description' => 'Panna cotta cremosa con coulis de frutos rojos',
                'price' => 5.90,
                'category_id' => $postres->id,
                'available' => true,
                'image_svg' => 'panna_cotta'
            ],
            [
                'name' => 'Cannoli Siciliani',
                'description' => 'Cannoli rellenos de ricotta con pistachos',
                'price' => 7.50,
                'category_id' => $postres->id,
                'available' => true,
                'image_svg' => 'cannoli'
            ],
            [
                'name' => 'Gelato Artigianale',
                'description' => 'Selección de helados artesanales (3 bolas)',
                'price' => 4.90,
                'category_id' => $postres->id,
                'available' => true,
                'image_svg' => 'gelato'
            ]
        ];

        // Insertar productos
        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['name' => $productData['name']],
                $productData
            );
        }
    }
}
