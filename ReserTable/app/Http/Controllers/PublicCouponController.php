<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiscountCoupon;
use App\Models\Client;
use Inertia\Inertia;
use Carbon\Carbon;

class PublicCouponController extends Controller
{
    /**
     * Get available coupons for a client
     */
    public function getAvailableCoupons(Request $request)
    {
        $clientId = $request->input('client_id');
        
        $coupons = collect();
        
        if ($clientId) {
            $client = Client::find($clientId);
            if ($client) {
                $coupons = DiscountCoupon::where('valid_from', '<=', now())
                    ->where('valid_to', '>=', now())
                    ->where(function ($query) {
                        $query->whereNull('max_uses')
                              ->orWhereRaw('used_count < max_uses');
                    })
                    ->get()
                    ->map(function ($coupon) {
                        return [
                            'id' => $coupon->id,
                            'code' => $coupon->code,
                            'description' => $coupon->description,
                            'discount_type' => $coupon->discount_type,
                            'value' => $coupon->value,
                            'valid_to' => $coupon->valid_to,
                            'minimum_amount' => $coupon->minimum_amount
                        ];
                    });
            }
        }

        return Inertia::render('CurrentPage', [
            'availableCoupons' => $coupons
        ]);
    }

    /**
     * Validate a coupon code
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'client_id' => 'nullable|integer|exists:clients,id'
        ]);

        $coupon = DiscountCoupon::where('code', $request->code)->first();

        if (!$coupon) {
            return back()->withErrors(['message' => 'Código de descuento no válido']);
        }

        // Verificar si el cupón está activo
        if ($coupon->valid_from > now() || $coupon->valid_to < now()) {
            return back()->withErrors(['message' => 'Este cupón ha expirado o aún no es válido']);
        }

        // Verificar si el cupón ha alcanzado el máximo de usos
        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            return back()->withErrors(['message' => 'Este cupón ha alcanzado el máximo de usos permitidos']);
        }

        return Inertia::render('CurrentPage', [
            'validationResult' => [
                'valid' => true,
                'coupon' => [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'description' => $coupon->description,
                    'discount_type' => $coupon->discount_type,
                    'value' => $coupon->value,
                    'minimum_amount' => $coupon->minimum_amount
                ]
            ]
        ]);
    }
}
