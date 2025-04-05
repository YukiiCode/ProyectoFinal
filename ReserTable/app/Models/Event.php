<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the offers associated with the event.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(EventOffer::class);
    }

    /**
     * Check if the event is currently active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $now = now()->startOfDay();
        $startDate = $this->start_date->startOfDay();
        $endDate = $this->end_date->endOfDay();
        
        return $now->between($startDate, $endDate);
    }

    /**
     * Check if the event is upcoming.
     *
     * @return bool
     */
    public function isUpcoming(): bool
    {
        return now()->startOfDay()->lt($this->start_date->startOfDay());
    }

    /**
     * Check if the event has ended.
     *
     * @return bool
     */
    public function hasEnded(): bool
    {
        return now()->startOfDay()->gt($this->end_date->endOfDay());
    }
}