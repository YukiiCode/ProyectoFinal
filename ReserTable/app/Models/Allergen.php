<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allergen extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
    ];

    /**
     * Relación con ingredientes
     */
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_allergens');
    }

    /**
     * Obtener productos que contienen este alérgeno
     */
    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            Ingredient::class,
            'id', // Foreign key en ingredient_allergens
            'id', // Foreign key en product_ingredients
            'id', // Local key en allergens
            'id'  // Local key en ingredients
        )->through('ingredient_allergens', 'product_ingredients');
    }
}
