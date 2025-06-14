<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Comentado porque ya existe un usuario admin en la base de datos
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            AllergenSeeder::class,
            IngredientSeeder::class,
            ProductCategorySeeder::class,
            ProductSeederNew::class,
            TableSeeder::class,
            ClientSeeder::class,
            DiscountCouponSeeder::class,
        ]);
    }
}
