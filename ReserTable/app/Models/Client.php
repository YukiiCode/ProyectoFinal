<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'name',
        'email',
        'phone',
    ];

    // Relación: un cliente puede tener muchas reservas
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Relación: un cliente puede tener muchos cupones de descuento personalizados
    public function discountCoupons()
    {
        return $this->hasMany(DiscountCoupon::class);
    }

    // Obtener cupones disponibles para el cliente
    public function getAvailableCoupons()
    {
        return DiscountCoupon::active()->forClient($this->id)->get();
    }
}
