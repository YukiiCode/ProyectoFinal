<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ingredient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'unit_of_measure',
        'allergen_info',
    ];

    /**
     * Get the products that use this ingredient.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_ingredients')
            ->withPivot('quantity_required')
            ->withTimestamps();
    }

    /**
     * Get the inventory record for this ingredient.
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }
}