<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'table_number',
        'capacity',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the reservations for the table.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Check if the table is available for reservation.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Set the table status to available.
     *
     * @return bool
     */
    public function markAsAvailable(): bool
    {
        $this->status = 'available';
        return $this->save();
    }

    /**
     * Set the table status to reserved.
     *
     * @return bool
     */
    public function markAsReserved(): bool
    {
        $this->status = 'reserved';
        return $this->save();
    }

    /**
     * Set the table status to occupied.
     *
     * @return bool
     */
    public function markAsOccupied(): bool
    {
        $this->status = 'occupied';
        return $this->save();
    }
}