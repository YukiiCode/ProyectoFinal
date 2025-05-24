<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $table = 'tables';

    protected $fillable = [
        'table_number',
        'capacity',
        'status',
        // Si tienes campos de posición, agrégalos aquí:
        'position_x',
        'position_y',
    ];

    // Relación: una mesa puede tener muchas reservas
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
