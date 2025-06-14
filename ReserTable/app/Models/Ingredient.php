<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit_of_measure',
    ];    /**
     * Relación con alérgenos
     */
    public function allergens()
    {
        return $this->belongsToMany(Allergen::class, 'ingredient_allergens');
    }

    /**
     * Relación directa con la tabla pivote ingredient_allergens
     */
    public function ingredientAllergens()
    {
        return $this->hasMany(IngredientAllergen::class);
    }

    /**
     * Relación con productos
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_ingredients')
                    ->withPivot('quantity_required');
    }

    /**
     * Inventario del ingrediente
     */
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
}
