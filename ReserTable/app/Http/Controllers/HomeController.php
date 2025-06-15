<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\DiscountCoupon;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        // Obtener productos destacados (los más populares o recomendados)
        $featuredProducts = Product::with(['category', 'ingredients.allergens'])
            ->where('available', true)
            ->limit(6)
            ->get()
            ->map(function ($product) {
                // Obtener alérgenos únicos del producto
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
                            'icon' => $allergen->icon ?? null
                        ];
                    }),
                    'image' => null // Por ahora sin imágenes
                ];
            });

        // Obtener categorías principales
        $categories = ProductCategory::withCount(['products' => function ($query) {
                $query->where('available', true);
            }])
            ->having('products_count', '>', 0)
            ->limit(8)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description ?? 'Deliciosas opciones de ' . $category->name,
                    'products_count' => $category->products_count,
                    'icon' => null
                ];
            });        // Obtener cupones activos y disponibles
        $coupons = DiscountCoupon::where('valid_from', '<=', now())
            ->where('valid_to', '>=', now())
            ->where(function ($query) {
                $query->whereNull('max_uses')
                      ->orWhereRaw('used_count < max_uses');
            })
            ->limit(4)
            ->get()
            ->map(function ($coupon) {
                return [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'description' => $coupon->description ?? 'Descuento especial',
                    'discount_type' => $coupon->discount_type,
                    'discount_value' => $coupon->value,
                    'expires_at' => $coupon->valid_to,
                    'usage_limit' => $coupon->max_uses,
                    'usage_count' => $coupon->used_count,
                    'minimum_amount' => $coupon->minimum_amount ?? 0
                ];
            });        return Inertia::render('Home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'coupons' => $coupons
        ]);
    }    /**
     * Mostrar la página de reservas públicas
     */
    public function reservas(): Response
    {
        // Obtener todas las mesas y calcular su estado real
        $tables = Table::all()->map(function ($table) {
            // Verificar si hay reservas activas para esta mesa hoy
            $now = now();
            $currentDate = $now->format('Y-m-d');
            $currentTime = $now->format('H:i');
              $activeReservation = \App\Models\Reservation::where('table_id', $table->id)
                ->where('reservation_date', $currentDate)
                ->whereIn('status', ['confirmed', 'pending'])
                ->get()
                ->first(function ($reservation) use ($currentTime) {
                    $reservationStart = $reservation->reservation_time;
                    $reservationEnd = \Carbon\Carbon::parse($reservationStart)->addHour()->format('H:i');
                    return $currentTime >= $reservationStart && $currentTime <= $reservationEnd;
                });
            
            // Determinar el estado real de la mesa
            $realStatus = $table->status; // Estado base de la mesa
            
            if ($table->status === 'available' && $activeReservation) {
                if ($activeReservation->status === 'confirmed') {
                    $realStatus = 'occupied';
                } else {
                    $realStatus = 'reserved';
                }
            }
            
            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
                'status' => $realStatus,
                'position_x' => $table->position_x ?? 50,
                'position_y' => $table->position_y ?? 50,
            ];
        });

        return Inertia::render('Reservas', [
            'tables' => $tables
        ]);
    }/**
     * Mostrar la página pública del menú
     */
    public function menu(): Response
    {
        // Obtener todos los productos disponibles con sus categorías y alérgenos
        $products = Product::with(['category', 'productIngredients.ingredient.ingredientAllergens.allergen'])
            ->where('available', true)
            ->orderBy('category_id')
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
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
                    'image_svg' => $product->image_svg,
                    'allergens' => $uniqueAllergens->map(function ($allergen) {
                        return [
                            'id' => $allergen->id,
                            'name' => $allergen->name,
                            'icon' => $allergen->icon ?? null
                        ];
                    })
                ];
            });

        // Obtener todas las categorías con productos disponibles
        $categories = ProductCategory::withCount(['products' => function ($query) {
                $query->where('available', true);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'products_count' => $category->products_count
                ];
            });

        return Inertia::render('Menu', [
            'products' => $products,
            'categories' => $categories
        ]);
    }
}
