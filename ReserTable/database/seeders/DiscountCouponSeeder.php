<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiscountCoupon;
use App\Models\Client;
use Carbon\Carbon;

class DiscountCouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cupones globales
        DiscountCoupon::create([
            'code' => 'BIENVENIDO20',
            'type' => 'global',
            'discount_type' => 'percentage',
            'value' => 20.00,
            'valid_from' => Carbon::today(),
            'valid_to' => Carbon::today()->addDays(30),
            'max_uses' => 100,
            'used_count' => 15,
        ]);

        DiscountCoupon::create([
            'code' => 'VERANO2025',
            'type' => 'global',
            'discount_type' => 'percentage',
            'value' => 15.00,
            'valid_from' => Carbon::today(),
            'valid_to' => Carbon::today()->addDays(90),
            'max_uses' => null, // Ilimitado
            'used_count' => 45,
        ]);

        DiscountCoupon::create([
            'code' => 'DESCUENTO5',
            'type' => 'global',
            'discount_type' => 'fixed',
            'value' => 5.00,
            'valid_from' => Carbon::today()->subDays(10),
            'valid_to' => Carbon::today()->addDays(20),
            'max_uses' => 50,
            'used_count' => 30,
        ]);

        // Cupón expirado para testing
        DiscountCoupon::create([
            'code' => 'EXPIRADO10',
            'type' => 'global',
            'discount_type' => 'percentage',
            'value' => 10.00,
            'valid_from' => Carbon::today()->subDays(60),
            'valid_to' => Carbon::today()->subDays(1),
            'max_uses' => 20,
            'used_count' => 18,
        ]);

        // Cupón agotado para testing
        DiscountCoupon::create([
            'code' => 'AGOTADO25',
            'type' => 'global',
            'discount_type' => 'percentage',
            'value' => 25.00,
            'valid_from' => Carbon::today()->subDays(5),
            'valid_to' => Carbon::today()->addDays(25),
            'max_uses' => 10,
            'used_count' => 10,
        ]);

        // Si existen clientes, crear algunos cupones personalizados
        $clients = Client::limit(3)->get();
        
        if ($clients->count() > 0) {
            foreach ($clients as $index => $client) {
                DiscountCoupon::create([
                    'code' => 'PERSONAL' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                    'type' => 'personalized',
                    'discount_type' => 'percentage',
                    'value' => 30.00,
                    'valid_from' => Carbon::today(),
                    'valid_to' => Carbon::today()->addDays(45),
                    'max_uses' => 1,
                    'used_count' => 0,
                    'client_id' => $client->id,
                ]);
            }
        }

        // Cupones de recompensa
        if ($clients->count() > 0) {
            DiscountCoupon::create([
                'code' => 'REWARDABC123',
                'type' => 'personalized',
                'discount_type' => 'fixed',
                'value' => 10.00,
                'valid_from' => Carbon::today(),
                'valid_to' => Carbon::today()->addDays(30),
                'max_uses' => 1,
                'used_count' => 0,
                'client_id' => $clients->first()->id,
            ]);
        }

        // Cupones especiales para eventos
        DiscountCoupon::create([
            'code' => 'ANIVERSARIO50',
            'type' => 'global',
            'discount_type' => 'percentage',
            'value' => 50.00,
            'valid_from' => Carbon::today()->addDays(10),
            'valid_to' => Carbon::today()->addDays(15),
            'max_uses' => 25,
            'used_count' => 0,
        ]);

        DiscountCoupon::create([
            'code' => 'NAVIDAD2025',
            'type' => 'global',
            'discount_type' => 'percentage',
            'value' => 35.00,
            'valid_from' => Carbon::create(2025, 12, 20),
            'valid_to' => Carbon::create(2025, 12, 31),
            'max_uses' => 200,
            'used_count' => 0,
        ]);
    }
}
