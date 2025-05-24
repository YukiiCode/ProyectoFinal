<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    public function run()
    {
        DB::table('tables')->truncate();

        DB::table('tables')->insert([
            [
                'table_number' => 1,
                'capacity' => 2,
                'status' => 0, // disponible
                'position_x' => 10,
                'position_y' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'table_number' => 2,
                'capacity' => 4,
                'status' => 1, // ocupada
                'position_x' => 30,
                'position_y' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'table_number' => 3,
                'capacity' => 6,
                'status' => 2, // reservada
                'position_x' => 60,
                'position_y' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'table_number' => 4,
                'capacity' => 2,
                'status' => 0, // disponible
                'position_x' => 80,
                'position_y' => 70,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'table_number' => 5,
                'capacity' => 4,
                'status' => 0, // disponible
                'position_x' => 50,
                'position_y' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
