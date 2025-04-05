<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventOffer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'discount_coupon_id',
    ];

    /**
     * Get the event that owns the offer.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the discount coupon associated with the offer.
     */
    public function discountCoupon(): BelongsTo
    {
        return $this->belongsTo(DiscountCoupon::class);
    }
}