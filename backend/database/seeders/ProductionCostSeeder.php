<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductionCostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            // Calculate production cost as 50-70% of the جملة price
            // This gives realistic profit margins
            $wholesalePrice = $product->price_جملة;
            
            if ($wholesalePrice > 0) {
                // Random percentage between 50% and 70%
                $costPercentage = rand(50, 70) / 100;
                $productionCost = round($wholesalePrice * $costPercentage, 2);
            } else {
                // Fallback if no wholesale price
                $productionCost = round($product->price_قطاعي * 0.6, 2);
            }
            
            $product->production_cost = $productionCost;
            $product->save();
            
            $this->command->info("Updated {$product->name_ar}: Production Cost = {$productionCost} EGP");
        }
        
        $this->command->info('✅ Production cost added to all products!');
    }
}
