<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
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
        'total_amount',
        'payment_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'payment_status' => 'string',
    ];

    /**
     * Get the client that owns the invoice.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the reservation associated with the invoice.
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the client history record associated with this invoice.
     */
    public function clientHistory(): HasOne
    {
        return $this->hasOne(ClientHistory::class);
    }

    /**
     * Check if the invoice is pending payment.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if the invoice is paid.
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if the invoice is cancelled.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->payment_status === 'cancelled';
    }

    /**
     * Mark the invoice as paid.
     *
     * @return bool
     */
    public function markAsPaid(): bool
    {
        $this->payment_status = 'paid';
        return $this->save();
    }

    /**
     * Mark the invoice as cancelled.
     *
     * @return bool
     */
    public function markAsCancelled(): bool
    {
        $this->payment_status = 'cancelled';
        return $this->save();
    }
}