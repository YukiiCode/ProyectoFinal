<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'table_id',
        'reservation_date',
        'party_size',
        'status',
        'discount_coupon_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reservation_date' => 'datetime',
        'party_size' => 'integer',
        'status' => 'string',
    ];

    /**
     * Get the client that owns the reservation.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the table associated with the reservation.
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    /**
     * Get the discount coupon applied to the reservation.
     */
    public function discountCoupon(): BelongsTo
    {
        return $this->belongsTo(DiscountCoupon::class, 'discount_coupon_id');
    }

    /**
     * Get the orders associated with the reservation.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the invoice associated with the reservation.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Check if the reservation is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the reservation is confirmed.
     *
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if the reservation is cancelled.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if the reservation is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Mark the reservation as confirmed.
     *
     * @return bool
     */
    public function confirm(): bool
    {
        $this->status = 'confirmed';
        return $this->save();
    }

    /**
     * Mark the reservation as cancelled.
     *
     * @return bool
     */
    public function cancel(): bool
    {
        $this->status = 'cancelled';
        return $this->save();
    }

    /**
     * Mark the reservation as completed.
     *
     * @return bool
     */
    public function complete(): bool
    {
        $this->status = 'completed';
        return $this->save();
    }
}