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
}
