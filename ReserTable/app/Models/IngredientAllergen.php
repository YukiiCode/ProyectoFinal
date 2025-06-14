<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientAllergen extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'ingredient_id',
        'allergen_id'
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function allergen()
    {
        return $this->belongsTo(Allergen::class);
    }
}
