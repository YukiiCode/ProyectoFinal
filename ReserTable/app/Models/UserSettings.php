<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSettings extends Model
{
    protected $fillable = [
        'user_id',
        'dark_mode',
        'language',
        'theme_color',
        'notifications_enabled',
        'sound_enabled',
        'sidebar_style',
        'dashboard_widgets',
    ];

    protected $casts = [
        'dark_mode' => 'boolean',
        'notifications_enabled' => 'boolean',
        'sound_enabled' => 'boolean',
        'dashboard_widgets' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaultSettings(): array
    {
        return [
            'dark_mode' => false,
            'language' => 'es',
            'theme_color' => 'blue',
            'notifications_enabled' => true,
            'sound_enabled' => true,
            'sidebar_style' => 'expanded',
            'dashboard_widgets' => [
                'stats' => true,
                'recent_reservations' => true,
                'quick_actions' => true,
                'calendar' => true,
            ],
        ];
    }
}
