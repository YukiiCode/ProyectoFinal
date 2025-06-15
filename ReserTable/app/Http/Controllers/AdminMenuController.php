<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminMenuController extends Controller
{
    /**
     * Display the menu management page
     */    public function index()
    {
        $products = Product::with('category')->orderBy('name')->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'category' => $product->category ? $product->category->name : 'Sin categoría',
                'category_id' => $product->category_id,
                'available' => $product->available,
                'image_path' => $product->image_svg ? (filter_var($product->image_svg, FILTER_VALIDATE_URL) ? $product->image_svg : '/storage/' . $product->image_svg) : null,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        return Inertia::render('Admin/Menu', [
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created product
     */    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'available' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_path' => 'nullable|string', // Para URLs externas
        ]);

        $imagePath = null;
        
        // Si se sube un archivo de imagen
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        } 
        // Si se proporciona una URL externa
        elseif ($request->filled('image_path')) {
            // Validar que es una URL válida
            if (filter_var($request->image_path, FILTER_VALIDATE_URL)) {
                $imagePath = $request->image_path;
            }
        }

        // Buscar la categoría por nombre o crear una nueva
        $category = \App\Models\ProductCategory::firstOrCreate(['name' => $request->category]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $category->id,
            'available' => $request->available ?? true,
            'image_svg' => $imagePath, // Usando la columna image_svg
        ]);

        return redirect()->route('admin.menu')->with('message', 'Producto creado exitosamente');
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'available' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_path' => 'nullable|string', // Para URLs externas
        ]);

        $imagePath = $product->image_svg;
        
        // Si se sube un nuevo archivo de imagen
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe y es un archivo local
            if ($imagePath && !filter_var($imagePath, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            
            $imagePath = $request->file('image')->store('products', 'public');
        } 
        // Si se proporciona una nueva URL externa
        elseif ($request->filled('image_path') && $request->image_path !== $product->image_svg) {
            // Validar que es una URL válida
            if (filter_var($request->image_path, FILTER_VALIDATE_URL)) {
                // Eliminar imagen anterior si es un archivo local
                if ($imagePath && !filter_var($imagePath, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->image_path;
            }
        }        // Buscar la categoría por nombre o crear una nueva
        $category = \App\Models\ProductCategory::firstOrCreate(['name' => $request->category]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $category->id,
            'available' => $request->available ?? true,
            'image_svg' => $imagePath, // Usando la columna image_svg
        ]);

        return redirect()->route('admin.menu')->with('message', 'Producto actualizado exitosamente');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        // Eliminar imagen si existe y es un archivo local
        if ($product->image_svg && !filter_var($product->image_svg, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($product->image_svg)) {
            Storage::disk('public')->delete($product->image_svg);
        }

        $product->delete();

        return redirect()->route('admin.menu')->with('message', 'Producto eliminado exitosamente');
    }

    /**
     * Toggle product availability
     */
    public function toggle(Request $request, Product $product)
    {
        $product->update(['available' => $request->boolean('available')]);

        return redirect()->route('admin.menu')->with('message', 
            $product->available ? 'Producto activado' : 'Producto desactivado'
        );
    }
}
