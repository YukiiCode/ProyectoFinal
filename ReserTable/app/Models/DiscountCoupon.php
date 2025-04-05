<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscountCoupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'value' => 'decimal:2',
    ];

    /**
     * Get the client that owns the discount coupon (if personalized).
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the reservations that use this discount coupon.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'discount_coupon_id');
    }

    /**
     * Get the event offers associated with this discount coupon.
     */
    public function eventOffers(): HasMany
    {
        return $this->hasMany(EventOffer::class);
    }

    /**
     * Check if the coupon is valid based on dates and usage.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $now = now()->startOfDay();
        $validFrom = $this->valid_from->startOfDay();
        $validTo = $this->valid_to->endOfDay();
        
        // Check if coupon is within valid date range
        if ($now->lt($validFrom) || $now->gt($validTo)) {
            return false;
        }
        
        // Check if coupon has reached max uses (if applicable)
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }
        
        return true;
    }
}