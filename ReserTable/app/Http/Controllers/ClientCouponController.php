<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiscountCoupon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientCouponController extends Controller
{
    /**
     * Get available coupons for the authenticated client
     */
    public function getAvailableCoupons()
    {
        $client = Auth::guard('client')->user();
        
        if (!$client) {
            return response()->json(['coupons' => []]);
        }
        
        $coupons = DiscountCoupon::where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->where(function($query) {
                $query->where('usage_limit', '>', 0)
                      ->orWhereNull('usage_limit');
            })
            ->get();
            
        return response()->json(['coupons' => $coupons]);
    }
}
