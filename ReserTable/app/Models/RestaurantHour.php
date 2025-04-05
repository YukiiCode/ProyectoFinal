<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantHour extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'day_of_week' => 'integer',
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
        'is_closed' => 'boolean',
    ];

    /**
     * Get the day name based on day_of_week value.
     *
     * @return string
     */
    public function getDayNameAttribute(): string
    {
        $days = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];

        return $days[$this->day_of_week] ?? 'Desconocido';
    }

    /**
     * Check if the restaurant is open at a specific time on this day.
     *
     * @param \DateTime|string $time
     * @return bool
     */
    public function isOpenAt($time): bool
    {
        if ($this->is_closed) {
            return false;
        }

        if (is_string($time)) {
            $time = \DateTime::createFromFormat('H:i', $time);
        }

        $openTime = \DateTime::createFromFormat('H:i', $this->open_time->format('H:i'));
        $closeTime = \DateTime::createFromFormat('H:i', $this->close_time->format('H:i'));
        $checkTime = \DateTime::createFromFormat('H:i', $time->format('H:i'));

        return $checkTime >= $openTime && $checkTime <= $closeTime;
    }
}