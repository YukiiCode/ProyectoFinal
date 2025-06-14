<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Allergen;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientMenuController extends Controller
{
    /**
     * Mostrar el menú para clientes
     */
    public function index(Request $request)
    {
        $categoryId = $request->get('category');
        $search = $request->get('search');

        // Obtener categorías
        $categories = ProductCategory::withCount('products')->get();        // Query base para productos
        $query = Product::with([
            'category', 
            'productIngredients.ingredient.ingredientAllergens.allergen'
        ])->where('available', true);

        // Filtrar por categoría si se especifica
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Filtrar por búsqueda si se especifica
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }        // Obtener productos con información de alérgenos
        $products = $query->get()->map(function ($product) {
            // Obtener alérgenos únicos del producto
            $allergens = collect();
            foreach ($product->productIngredients as $productIngredient) {
                foreach ($productIngredient->ingredient->ingredientAllergens as $allergenRelation) {
                    $allergens->push($allergenRelation->allergen);
                }
            }
            $uniqueAllergens = $allergens->unique('id')->values();

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'category' => $product->category->name ?? 'Sin categoría',
                'category_id' => $product->category_id,
                'allergens' => $uniqueAllergens->map(function ($allergen) {
                    return [
                        'id' => $allergen->id,
                        'name' => $allergen->name,
                        'description' => $allergen->description,
                        'icon' => $allergen->icon,
                    ];
                }),
                'ingredients' => $product->productIngredients->map(function ($productIngredient) {
                    return [
                        'id' => $productIngredient->ingredient->id,
                        'name' => $productIngredient->ingredient->name,
                        'quantity' => $productIngredient->quantity_required,
                        'unit' => $productIngredient->ingredient->unit_of_measure,
                    ];
                }),
            ];
        });

        // Obtener todos los alérgenos para filtros
        $allAllergens = Allergen::all();

        return Inertia::render('Client/Menu', [
            'products' => $products,
            'categories' => $categories,
            'allergens' => $allAllergens,
            'filters' => [
                'category' => $categoryId,
                'search' => $search,
            ],
        ]);
    }

    /**
     * Obtener información detallada de un producto
     */
    public function show($id)
    {
        $product = Product::with(['category', 'ingredients.allergens'])
            ->where('available', true)
            ->findOrFail($id);

        // Obtener alérgenos únicos del producto
        $allergens = $product->ingredients
            ->pluck('allergens')
            ->flatten()
            ->unique('id')
            ->values();

        $productData = [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'category' => $product->category->name ?? 'Sin categoría',
            'allergens' => $allergens->map(function ($allergen) {
                return [
                    'id' => $allergen->id,
                    'name' => $allergen->name,
                    'description' => $allergen->description,
                    'icon' => $allergen->icon,
                ];
            }),
            'ingredients' => $product->ingredients->map(function ($ingredient) {
                return [
                    'id' => $ingredient->id,
                    'name' => $ingredient->name,
                    'quantity' => $ingredient->pivot->quantity_required ?? null,
                    'unit' => $ingredient->unit_of_measure,
                    'allergens' => $ingredient->allergens->map(function ($allergen) {
                        return [
                            'id' => $allergen->id,
                            'name' => $allergen->name,
                            'description' => $allergen->description,
                            'icon' => $allergen->icon,
                        ];
                    }),
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'product' => $productData,
        ]);
    }

    /**
     * Buscar productos por alérgeno
     */
    public function searchByAllergen(Request $request)
    {
        $allergenId = $request->get('allergen_id');
        $exclude = $request->get('exclude', false); // true para excluir productos con este alérgeno

        if (!$allergenId) {
            return response()->json([
                'success' => false,
                'message' => 'ID de alérgeno requerido',
            ], 422);
        }

        $query = Product::with(['category', 'ingredients.allergens'])
            ->where('available', true);

        if ($exclude) {
            // Excluir productos que contengan este alérgeno
            $query->whereDoesntHave('ingredients.allergens', function ($q) use ($allergenId) {
                $q->where('allergens.id', $allergenId);
            });
        } else {
            // Incluir solo productos que contengan este alérgeno
            $query->whereHas('ingredients.allergens', function ($q) use ($allergenId) {
                $q->where('allergens.id', $allergenId);
            });
        }

        $products = $query->get()->map(function ($product) {
            $allergens = $product->ingredients
                ->pluck('allergens')
                ->flatten()
                ->unique('id')
                ->values();

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'category' => $product->category->name ?? 'Sin categoría',
                'allergens' => $allergens->map(function ($allergen) {
                    return [
                        'id' => $allergen->id,
                        'name' => $allergen->name,
                        'description' => $allergen->description,
                        'icon' => $allergen->icon,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }
}
