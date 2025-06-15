<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class UpdateProblemImages extends Command
{
    protected $signature = 'images:fix-problem';
    protected $description = 'Fix problematic product images with working URLs';

    public function handle()
    {
        $this->info('Actualizando imágenes problemáticas...');

        // URLs de imágenes que funcionan bien
        $imageUpdates = [
            // Vinos
            'Chianti Classico DOCG' => 'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?w=800&q=80',
            'Barolo DOCG' => 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=800&q=80',
            'Brunello di Montalcino' => 'https://images.unsplash.com/photo-1568213816046-0ee1c42bd559?w=800&q=80',
            
            // Entrantes
            'Bruschetta Italiana' => 'https://images.unsplash.com/photo-1572441713132-51c75654db73?w=800&q=80',
            'Bruschetta Tricolore' => 'https://images.unsplash.com/photo-1601312946458-20375bb75065?w=800&q=80',
            'Caprese di Bufala' => 'https://images.unsplash.com/photo-1608877907149-41d1fbfd8241?w=800&q=80',
            'Carpaccio di Manzo' => 'https://images.unsplash.com/photo-1594007654729-407eedc4be65?w=800&q=80',
            'Antipasto della Casa' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&q=80',
            
            // Pastas
            'Spaghetti Carbonara' => 'https://images.unsplash.com/photo-1621996346565-e3dbc353d2e5?w=800&q=80',
            'Spaghetti Cacio e Pepe' => 'https://images.unsplash.com/photo-1579952363873-27d3bfad9c0d?w=800&q=80',
            'Penne Arrabbiata' => 'https://images.unsplash.com/photo-1563379091339-03246963d7d3?w=800&q=80',
            'Fettuccine Alfredo' => 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=800&q=80',
            
            // Postres
            'Panna Cotta' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=800&q=80',
            'Panna Cotta ai Frutti Rossi' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=800&q=80',
            'Tiramisù della Casa' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=800&q=80',
            'Cannoli Siciliani' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&q=80',
            
            // Pizzas
            'Pizza Margherita' => 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=800&q=80',
            'Pizza Quattro Stagioni' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=800&q=80',
            'Pizza Diavola' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=800&q=80',
            
            // Carnes
            'Bistecca alla Fiorentina' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=800&q=80',
            'Osso Buco alla Milanese' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&q=80',
            
            // Pescados
            'Branzino in Crosta di Sale' => 'https://images.unsplash.com/photo-1559847844-d7b4ca63b943?w=800&q=80',
            'Salmone Grigliato' => 'https://images.unsplash.com/photo-1467003860645-c59c11341650?w=800&q=80',
            
            // Bebidas
            'Espresso Romano' => 'https://images.unsplash.com/photo-1510707577719-ae7c14805e3a?w=800&q=80',
            'Cappuccino Italiano' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=800&q=80',
            'Acqua Naturale' => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=800&q=80',
            'Acqua Frizzante' => 'https://images.unsplash.com/photo-1523362628745-0c100150b504?w=800&q=80',
        ];

        $updated = 0;
        $total = count($imageUpdates);

        foreach ($imageUpdates as $productName => $imageUrl) {
            $product = Product::where('name', $productName)->first();
            
            if ($product) {
                $product->update(['image_svg' => $imageUrl]);
                $this->info("✅ Actualizado: {$productName}");
                $updated++;
            } else {
                $this->warn("⚠️  No encontrado: {$productName}");
            }
        }

        $this->info("\n🎉 Proceso completado!");
        $this->info("📊 Productos actualizados: {$updated}/{$total}");
        
        return Command::SUCCESS;
    }
}
