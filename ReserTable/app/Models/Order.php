<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'reservation_id',
        'status',
        'order_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Get the client that owns the order.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the reservation associated with the order.
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Calculate the total amount of the order.
     *
     * @return float
     */
    public function calculateTotal(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }

    /**
     * Check if the order is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the order is in progress.
     *
     * @return bool
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if the order is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the order is cancelled.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Mark the order as in progress.
     *
     * @return bool
     */
    public function markAsInProgress(): bool
    {
        $this->status = 'in_progress';
        return $this->save();
    }

    /**
     * Mark the order as completed.
     *
     * @return bool
     */
    public function markAsCompleted(): bool
    {
        $this->status = 'completed';
        return $this->save();
    }

    /**
     * Mark the order as cancelled.
     *
     * @return bool
     */
    public function markAsCancelled(): bool
    {
        $this->status = 'cancelled';
        return $this->save();
    }
}