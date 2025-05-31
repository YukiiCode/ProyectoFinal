<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        DB::table('products')->insertOrIgnore([
            [
                'name' => 'Lasaña Clásica',
                'description' => 'Capas de pasta fresca, ragú de ternera, bechamel y queso parmesano.',
                'price' => 14.50,
                'category_id' => 2,
                'available' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Pizza Margherita',
                'description' => 'Salsa de tomate San Marzano, mozzarella fresca, albahaca y aceite de oliva virgen extra.',
                'price' => 12.00,
                'category_id' => 2,
                'available' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Risotto ai Funghi Porcini',
                'description' => 'Arroz Carnaroli cremoso con setas porcini frescas y un toque de trufa.',
                'price' => 16.00,
                'category_id' => 2,
                'available' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Tiramisú Casero',
                'description' => 'Bizcochos de soletilla bañados en café, crema de mascarpone y cacao.',
                'price' => 7.50,
                'category_id' => 3,
                'available' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Agua Mineral',
                'description' => 'Botella de agua mineral natural.',
                'price' => 2.00,
                'category_id' => 4,
                'available' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Coca-Cola',
                'description' => 'Refresco clásico.',
                'price' => 2.50,
                'category_id' => 4,
                'available' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
