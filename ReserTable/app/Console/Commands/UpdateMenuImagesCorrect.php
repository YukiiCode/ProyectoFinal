<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class UpdateMenuImagesCorrect extends Command
{
    protected $signature = 'menu:update-images-correct';
    protected $description = 'Update menu items with correct thematic images';

    public function handle()
    {
        $this->info('Actualizando imágenes del menú con fotos temáticas correctas...');

        // Mapeo de productos específicos con imágenes temáticas apropiadas
        $productImages = [
            // Antipastos
            'Antipasto della Casa' => 'https://images.unsplash.com/photo-1571197140266-204651a1dd84?w=800&q=80', // Plato de antipastos variados
            'Bruschetta Italiana' => 'https://images.unsplash.com/photo-1572441713132-51c75654db73?w=800&q=80', // Bruschetta con tomate
            'Bruschetta Tricolore' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&q=80', // Bruschetta tricolor
            'Caprese di Bufala' => 'https://images.unsplash.com/photo-1608039755401-742074f0548d?w=800&q=80', // Caprese con mozzarella
            'Carpaccio di Manzo' => 'https://images.unsplash.com/photo-1551782450-17144efb9c50?w=800&q=80', // Carpaccio de carne
            
            // Pastas
            'Spaghetti Carbonara' => 'https://images.unsplash.com/photo-1621996346565-e3dbc353d2e5?w=800&q=80', // Carbonara auténtica
            'Penne all\'Arrabbiata' => 'https://images.unsplash.com/photo-1621647332298-4ad5b6b50b8c?w=800&q=80', // Penne arrabbiata
            'Fettuccine Alfredo' => 'https://images.unsplash.com/photo-1645112411341-6c4fd023714a?w=800&q=80', // Fettuccine cremoso
            'Lasagne della Nonna' => 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=800&q=80', // Lasagna casera
            'Tagliatelle ai Porcini' => 'https://images.unsplash.com/photo-1572441713132-51c75654db73?w=800&q=80', // Pasta con hongos
            
            // Secondi Piatti
            'Scaloppine al Limone' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80', // Escalope de ternera
            'Pollo alla Parmigiana' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=800&q=80', // Pollo parmigiana
            'Branzino al Sale' => 'https://images.unsplash.com/photo-1559737558-2f5a35f4523c?w=800&q=80', // Pescado al horno
            'Salmone alla Griglia' => 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=800&q=80', // Salmón a la parrilla
            'Bistecca alla Fiorentina' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=800&q=80', // Bistec florentino
            
            // Pizzas
            'Pizza Margherita' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800&q=80', // Pizza margarita clásica
            'Pizza Quattro Stagioni' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=800&q=80', // Pizza cuatro estaciones
            'Pizza Diavola' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=800&q=80', // Pizza picante
            
            // Dolci
            'Tiramisu' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=800&q=80', // Tiramisú auténtico
            'Panna Cotta' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=800&q=80', // Panna cotta clásica
            'Panna Cotta ai Frutti Rossi' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=800&q=80', // Panna cotta con frutos rojos
            'Cannoli Siciliani' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=800&q=80', // Cannoli sicilianos
            'Gelato Artigianale' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=800&q=80', // Gelato artesanal
            
            // Vinos
            'Chianti Classico DOCG' => 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=800&q=80', // Vino tinto italiano
            'Prosecco di Valdobbiadene' => 'https://images.unsplash.com/photo-1547595628-c61a29f496f0?w=800&q=80', // Prosecco con copa
            
            // Bebidas
            'Espresso Romano' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800&q=80', // Espresso italiano
            'Cappuccino' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=800&q=80', // Cappuccino con arte latte
            'Limonata Fresca' => 'https://images.unsplash.com/photo-1621263764928-df1444c5e859?w=800&q=80', // Limonada fresca
        ];

        $updated = 0;
        $total = count($productImages);

        foreach ($productImages as $productName => $imageUrl) {
            $product = Product::where('name', 'LIKE', "%{$productName}%")->first();
            
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
