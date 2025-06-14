<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with([
            'category',
            'productIngredients.ingredient.ingredientAllergens.allergen'
        ])->available();

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $products = $query->get()->map(function ($product) {
            // Obtener alérgenos únicos del producto
            $allergens = collect();
            foreach ($product->productIngredients as $productIngredient) {
                foreach ($productIngredient->ingredient->ingredientAllergens as $allergenRelation) {
                    $allergens->push([
                        'id' => $allergenRelation->allergen->id,
                        'name' => $allergenRelation->allergen->name,
                        'icon' => $allergenRelation->allergen->icon,
                    ]);
                }
            }
            
            $product->allergens = $allergens->unique('id')->values();
            return $product;
        });

        // Agrupar productos por categoría
        $productsByCategory = $products->groupBy('category.name');

        $categories = ProductCategory::all();

        return Inertia::render('Menu', [
            'productsByCategory' => $productsByCategory,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id'])
        ]);
    }
}
