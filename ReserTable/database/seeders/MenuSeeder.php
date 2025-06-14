<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Ingredient;
use App\Models\Allergen;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // Crear categorías
        $categorias = [
            ['name' => 'Entrantes', 'description' => 'Deliciosos aperitivos para comenzar'],
            ['name' => 'Pasta', 'description' => 'Pasta fresca casera con salsas tradicionales'],
            ['name' => 'Pizza', 'description' => 'Pizzas artesanales con masa fermentada'],
            ['name' => 'Carnes', 'description' => 'Carnes selectas a la parrilla'],
            ['name' => 'Pescados', 'description' => 'Pescados frescos del mediterráneo'],
            ['name' => 'Postres', 'description' => 'Dulces tradicionales italianos'],
            ['name' => 'Bebidas', 'description' => 'Selección de bebidas y vinos'],
        ];        foreach ($categorias as $categoria) {
            ProductCategory::firstOrCreate(['name' => $categoria['name']], $categoria);
        }

        // Crear alérgenos
        $alergenos = [
            ['name' => 'Gluten', 'icon' => '🌾'],
            ['name' => 'Lácteos', 'icon' => '🥛'],
            ['name' => 'Huevos', 'icon' => '🥚'],
            ['name' => 'Frutos Secos', 'icon' => '🥜'],
            ['name' => 'Mariscos', 'icon' => '🦐'],
            ['name' => 'Pescado', 'icon' => '🐟'],
            ['name' => 'Soja', 'icon' => '🫘'],
            ['name' => 'Apio', 'icon' => '🥬'],
        ];

        foreach ($alergenos as $alergeno) {
            Allergen::firstOrCreate(['name' => $alergeno['name']], $alergeno);
        }

        // Obtener las categorías e IDs
        $entrantes = ProductCategory::where('name', 'Entrantes')->first()->id;
        $pasta = ProductCategory::where('name', 'Pasta')->first()->id;
        $pizza = ProductCategory::where('name', 'Pizza')->first()->id;
        $carnes = ProductCategory::where('name', 'Carnes')->first()->id;
        $pescados = ProductCategory::where('name', 'Pescados')->first()->id;
        $postres = ProductCategory::where('name', 'Postres')->first()->id;
        $bebidas = ProductCategory::where('name', 'Bebidas')->first()->id;

        // Productos con imágenes SVG embebidas
        $productos = [
            // Entrantes
            [
                'name' => 'Bruschetta Tricolore',
                'description' => 'Pan tostado con tomate, mozzarella fresca y albahaca. Un clásico italiano que celebra los colores de la bandera.',
                'price' => 8.50,
                'category_id' => $entrantes,
                'available' => true,
                'image_svg' => 'bruschetta'
            ],
            [
                'name' => 'Antipasto della Casa',
                'description' => 'Selección de embutidos italianos, quesos curados, aceitunas y vegetales marinados.',
                'price' => 14.90,
                'category_id' => $entrantes,
                'available' => true,
                'image_svg' => 'antipasto'
            ],
            [
                'name' => 'Caprese di Bufala',
                'description' => 'Mozzarella de búfala DOP, tomates cherry confitados y reducción de balsámico.',
                'price' => 12.50,
                'category_id' => $entrantes,
                'available' => true,
                'image_svg' => 'caprese'
            ],

            // Pasta
            [
                'name' => 'Spaghetti Carbonara',
                'description' => 'La auténtica receta romana con guanciale, huevo, pecorino y pimienta negra.',
                'price' => 13.90,
                'category_id' => $pasta,
                'available' => true,
                'image_svg' => 'carbonara'
            ],
            [
                'name' => 'Fettuccine Alfredo',
                'description' => 'Pasta fresca con salsa cremosa de mantequilla, parmesano y un toque de nuez moscada.',
                'price' => 12.50,
                'category_id' => $pasta,
                'available' => true,
                'image_svg' => 'alfredo'
            ],
            [
                'name' => 'Lasagna della Nonna',
                'description' => 'Lasaña casera con ragú bolognés, bechamel y tres quesos, gratinada al horno.',
                'price' => 15.90,
                'category_id' => $pasta,
                'available' => true,
                'image_svg' => 'lasagna'
            ],

            // Pizza
            [
                'name' => 'Pizza Margherita',
                'description' => 'La pizza original con salsa de tomate San Marzano, mozzarella di bufala y albahaca fresca.',
                'price' => 11.90,
                'category_id' => $pizza,
                'available' => true,
                'image_svg' => 'margherita'
            ],
            [
                'name' => 'Pizza Quattro Stagioni',
                'description' => 'Cuatro estaciones en una pizza: alcachofas, jamón, champiñones y aceitunas.',
                'price' => 16.50,
                'category_id' => $pizza,
                'available' => true,
                'image_svg' => 'quattro_stagioni'
            ],
            [
                'name' => 'Pizza Prosciutto e Funghi',
                'description' => 'Jamón serrano, champiñones porcini y mozzarella sobre masa fermentada 48h.',
                'price' => 14.90,
                'category_id' => $pizza,
                'available' => true,
                'image_svg' => 'prosciutto_funghi'
            ],

            // Carnes
            [
                'name' => 'Osso Buco alla Milanese',
                'description' => 'Jarrete de ternera braseado con verduras, vino blanco y gremolata.',
                'price' => 22.90,
                'category_id' => $carnes,
                'available' => true,
                'image_svg' => 'osso_buco'
            ],
            [
                'name' => 'Bistecca alla Fiorentina',
                'description' => 'Chuletón de 800g a la parrilla con aceite de oliva, limón y sal marina.',
                'price' => 45.00,
                'category_id' => $carnes,
                'available' => true,
                'image_svg' => 'bistecca'
            ],

            // Pescados
            [
                'name' => 'Branzino in Crosta di Sale',
                'description' => 'Lubina fresca cocinada en costra de sal marina con hierbas aromáticas.',
                'price' => 24.90,
                'category_id' => $pescados,
                'available' => true,
                'image_svg' => 'branzino'
            ],
            [
                'name' => 'Risotto ai Frutti di Mare',
                'description' => 'Arroz cremoso con mejillones, almejas, gambas y calamares frescos.',
                'price' => 19.90,
                'category_id' => $pescados,
                'available' => true,
                'image_svg' => 'risotto_mare'
            ],

            // Postres
            [
                'name' => 'Tiramisù della Casa',
                'description' => 'El postre más famoso de Italia con mascarpone, café y cacao.',
                'price' => 6.90,
                'category_id' => $postres,
                'available' => true,
                'image_svg' => 'tiramisu'
            ],
            [
                'name' => 'Panna Cotta ai Frutti Rossi',
                'description' => 'Crema sedosa de vainilla con coulis de frutos rojos de temporada.',
                'price' => 5.90,
                'category_id' => $postres,
                'available' => true,
                'image_svg' => 'panna_cotta'
            ],
            [
                'name' => 'Cannoli Siciliani',
                'description' => 'Dos cannoli crujientes rellenos de ricotta fresca con pistachos.',
                'price' => 7.50,
                'category_id' => $postres,
                'available' => true,
                'image_svg' => 'cannoli'
            ],

            // Bebidas
            [
                'name' => 'Chianti Classico DOCG',
                'description' => 'Vino tinto toscano con denominación de origen controlada.',
                'price' => 28.00,
                'category_id' => $bebidas,
                'available' => true,
                'image_svg' => 'wine'
            ],
            [
                'name' => 'Limoncello della Casa',
                'description' => 'Licor artesanal de limones de la costa amalfitana.',
                'price' => 4.50,
                'category_id' => $bebidas,
                'available' => true,
                'image_svg' => 'limoncello'
            ],
        ];        foreach ($productos as $producto) {
            Product::firstOrCreate(['name' => $producto['name']], $producto);
        }

        $this->command->info('Menú completo creado con éxito!');
    }
}
