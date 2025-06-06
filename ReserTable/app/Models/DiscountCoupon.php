<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class DiscountCoupon extends Model
{
    use SoftDeletes;
    
    protected $table = 'discount_coupons';

    protected $fillable = [
        'code',
        'type',
        'discount_type',
        'value',
        'valid_from',
        'valid_to',
        'max_uses',
        'used_count',
        'client_id',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'value' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
    ];

    protected $appends = [
        'is_valid',
        'is_expired',
        'usage_percentage',
        'formatted_discount'
    ];

    // Relaciones
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Scopes
    public function scopeValid($query)
    {
        $today = Carbon::today();
        return $query->where('valid_from', '<=', $today)
                    ->where('valid_to', '>=', $today);
    }

    public function scopeActive($query)
    {
        return $query->where('valid_from', '<=', Carbon::today())
                    ->where('valid_to', '>=', Carbon::today())
                    ->where(function ($q) {
                        $q->whereNull('max_uses')
                          ->orWhereColumn('used_count', '<', 'max_uses');
                    });
    }

    public function scopeGlobal($query)
    {
        return $query->where('type', 'global');
    }

    public function scopePersonalized($query)
    {
        return $query->where('type', 'personalized');
    }

    public function scopeUsed($query)
    {
        return $query->where('used_count', '>', 0);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where(function ($q) use ($clientId) {
            $q->where('type', 'global')
              ->orWhere(function ($subQ) use ($clientId) {
                  $subQ->where('type', 'personalized')
                       ->where('client_id', $clientId);
              });
        });
    }

    // Accessors
    public function getIsValidAttribute()
    {
        $today = Carbon::today();
        return $today->between($this->valid_from, $this->valid_to) && 
               (!$this->max_uses || $this->used_count < $this->max_uses);
    }

    public function getIsExpiredAttribute()
    {
        return Carbon::today()->gt($this->valid_to);
    }

    public function getUsagePercentageAttribute()
    {
        if (!$this->max_uses) {
            return 0;
        }
        return round(($this->used_count / $this->max_uses) * 100, 2);
    }

    public function getFormattedDiscountAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return $this->value . '%';
        }
        return '$' . number_format($this->value, 2);
    }

    // Métodos auxiliares
    public function canBeUsed($clientId = null)
    {
        // Verificar si está vigente
        if (!$this->is_valid) {
            return false;
        }

        // Si es personalizado, verificar que pertenezca al cliente
        if ($this->type === 'personalized' && $this->client_id !== $clientId) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        if ($this->discount_type === 'percentage') {
            return $amount * ($this->value / 100);
        }
        
        // Para descuento fijo, no puede ser mayor al monto total
        return min($this->value, $amount);
    }

    public function getDiscountAmount()
    {
        // Si es porcentaje, devolvemos el valor del porcentaje
        // Si es fijo, devolvemos el valor fijo
        return $this->value;
    }

    public function markAsUsed()
    {
        $this->increment('used_count');
    }

    public function getRemainingUsesAttribute()
    {
        if (!$this->max_uses) {
            return 'Ilimitado';
        }
        
        $remaining = $this->max_uses - $this->used_count;
        return max(0, $remaining);
    }

    public function getStatusAttribute()
    {
        if ($this->is_expired) {
            return 'expired';
        }
        
        if (!$this->is_valid) {
            return 'inactive';
        }
        
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return 'depleted';
        }
        
        return 'active';
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'expired':
                return 'Expirado';
            case 'inactive':
                return 'Inactivo';
            case 'depleted':
                return 'Agotado';
            case 'active':
                return 'Activo';
            default:
                return 'Desconocido';
        }
    }

    // Métodos estáticos
    public static function generateUniqueCode($prefix = '')
    {
        do {
            $code = $prefix . strtoupper(\Illuminate\Support\Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public static function findByCode($code)
    {
        return self::where('code', strtoupper($code))->first();
    }

    public static function validateForClient($code, $clientId = null)
    {
        $coupon = self::findByCode($code);

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'Código de descuento no encontrado',
                'coupon' => null
            ];
        }

        if (!$coupon->canBeUsed($clientId)) {
            $status = $coupon->status;
            
            switch ($status) {
                case 'expired':
                    $message = 'El código de descuento ha expirado';
                    break;
                case 'depleted':
                    $message = 'El código de descuento ha alcanzado su límite de usos';
                    break;
                case 'inactive':
                    $message = 'El código de descuento no está vigente';
                    break;
                default:
                    $message = 'El código de descuento no está disponible para tu cuenta';
            }

            return [
                'valid' => false,
                'message' => $message,
                'coupon' => null
            ];
        }

        return [
            'valid' => true,
            'message' => 'Código de descuento válido',
            'coupon' => $coupon
        ];
    }

    public static function createRewardCoupon($clientId, $discountType, $value, $daysValid = 30)
    {
        $code = self::generateUniqueCode('REWARD');

        return self::create([
            'code' => $code,
            'type' => 'personalized',
            'discount_type' => $discountType,
            'value' => $value,
            'valid_from' => Carbon::today(),
            'valid_to' => Carbon::today()->addDays($daysValid),
            'max_uses' => 1,
            'used_count' => 0,
            'client_id' => $clientId,
        ]);
    }
}
