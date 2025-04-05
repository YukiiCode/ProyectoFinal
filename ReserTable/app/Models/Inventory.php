<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ingredient_id',
        'quantity_in_stock',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity_in_stock' => 'decimal:2',
    ];

    /**
     * Get the ingredient that owns the inventory record.
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    /**
     * Check if the ingredient is in stock.
     *
     * @param float $quantity
     * @return bool
     */
    public function isInStock(float $quantity = 1): bool
    {
        return $this->quantity_in_stock >= $quantity;
    }

    /**
     * Reduce the stock quantity.
     *
     * @param float $quantity
     * @return bool
     */
    public function reduceStock(float $quantity): bool
    {
        if (!$this->isInStock($quantity)) {
            return false;
        }

        $this->quantity_in_stock -= $quantity;
        return $this->save();
    }

    /**
     * Increase the stock quantity.
     *
     * @param float $quantity
     * @return bool
     */
    public function increaseStock(float $quantity): bool
    {
        $this->quantity_in_stock += $quantity;
        return $this->save();
    }
}