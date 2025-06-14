<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;
    
    protected $table = 'reservations';

    protected $fillable = [
        'client_id',
        'table_id',
        'reservation_date',
        'party_size',
        'status',
        'discount_coupon_id',
        'special_requests',
    ];

    protected $casts = [
        'reservation_date' => 'datetime',
    ];

    // Relación: una reserva pertenece a una mesa
    public function table()
    {
        return $this->belongsTo(Table::class);
    }
    
    // Relación: una reserva pertenece a un cliente
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    // Relación: una reserva puede tener un cupón de descuento
    public function discountCoupon()
    {
        return $this->belongsTo(DiscountCoupon::class, 'discount_coupon_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeFuture($query)
    {
        return $query->where('reservation_date', '>', now());
    }
}
