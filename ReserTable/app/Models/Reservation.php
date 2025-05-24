<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = 'reservations';

    protected $fillable = [
        'client_id',
        'table_id',
        'reservation_date',
        'party_size',
        'status',
        'discount_coupon_id',
    ];

    // Relación: una reserva pertenece a una mesa
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    // Relación: una reserva pertenece a un cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relación: una reserva puede tener un cupón de descuento
    public function discountCoupon()
    {
        return $this->belongsTo(DiscountCoupon::class, 'discount_coupon_id');
    }
}
