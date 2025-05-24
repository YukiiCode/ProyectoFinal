<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCoupon extends Model
{
    protected $table = 'discount_coupons';

    protected $fillable = [
        'code',
        'type',
        'discount_type',
        'value',
        'valid_from',
        'valid_to',
        'max_uses',
        'used_count',
        'client_id',
    ];

    // Relación: un cupón puede pertenecer a un cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
