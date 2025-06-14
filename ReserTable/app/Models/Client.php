<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'clients';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
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
