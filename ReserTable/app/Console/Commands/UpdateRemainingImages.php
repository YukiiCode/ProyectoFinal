<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class UpdateRemainingImages extends Command
{
    protected $signature = 'menu:update-remaining-images';
    protected $description = 'Update remaining menu items with correct images';

    public function handle()
    {
        $this->info('Actualizando productos restantes con imágenes correctas...');

        // Mapeo de productos restantes con nombres exactos
        $productImages = [
            'Antipasto Misto' => 'https://images.unsplash.com/photo-1571197140266-204651a1dd84?w=800&q=80',
            'Lasagna della Nonna' => 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=800&q=80',
            'Ravioli ai Funghi' => 'https://images.unsplash.com/photo-1563379091339-03246963d51a?w=800&q=80',
            'Pizza Margherita' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800&q=80',
            'Pizza Prosciutto' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=800&q=80',
            'Pizza Prosciutto e Funghi' => 'https://images.unsplash.com/photo-1571066811602-716837d681de?w=800&q=80',
            'Osso Buco Milanese' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=800&q=80',
            'Osso Buco alla Milanese' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=800&q=80',
            'Saltimbocca alla Romana' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80',
            'Branzino in Crosta' => 'https://images.unsplash.com/photo-1559737558-2f5a35f4523c?w=800&q=80',
            'Branzino in Crosta di Sale' => 'https://images.unsplash.com/photo-1559737558-2f5a35f4523c?w=800&q=80',
            'Risotto ai Frutti di Mare' => 'https://images.unsplash.com/photo-1563379091339-03246963d51a?w=800&q=80',
            'Tiramisu della Casa' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=800&q=80',
            'Limoncello della Casa' => 'https://images.unsplash.com/photo-1621263764928-df1444c5e859?w=800&q=80',
        ];

        $updated = 0;
        $total = count($productImages);

        foreach ($productImages as $productName => $imageUrl) {
            $product = Product::where('name', $productName)->first();
            
            if ($product) {
                $product->update(['image_svg' => $imageUrl]);
                $this->info("✅ Actualizado: {$product->name}");
                $updated++;
            } else {
                $this->warn("❌ No encontrado: {$productName}");
            }
        }

        $this->info("🎉 Proceso completado!");
        $this->info("📊 Productos actualizados: {$updated}/{$total}");
        
        return 0;
    }
}
