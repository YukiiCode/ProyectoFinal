<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        DB::table('product_categories')->insertOrIgnore([
            [
                'id' => 1,
                'name' => 'Entrantes',
                'description' => 'Platos para abrir el apetito',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Principales',
                'description' => 'Platos principales',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Postres',
                'description' => 'Dulces y postres',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Bebidas',
                'description' => 'Bebidas frías y calientes',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
