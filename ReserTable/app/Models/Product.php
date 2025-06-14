<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'available',
        'image_svg',
    ];

    protected $casts = [
        'available' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Relación con categoría
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }    /**
     * Relación con ingredientes del producto
     */
    public function productIngredients()
    {
        return $this->hasMany(ProductIngredient::class);
    }

    /**
     * Relación con ingredientes
     */
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredients')
                    ->withPivot('quantity_required');
    }

    /**
     * Obtener alérgenos únicos del producto
     */
    public function getAllergensAttribute()
    {
        $allergens = collect();
        
        foreach ($this->productIngredients as $productIngredient) {
            foreach ($productIngredient->ingredient->ingredientAllergens as $allergenRelation) {
                $allergens->push($allergenRelation->allergen);
            }
        }
        
        return $allergens->unique('id')->values();
    }
}
