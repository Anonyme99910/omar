<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add new columns
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price_جملة', 10, 2)->default(0)->after('selling_price');
            $table->decimal('price_قطاعي', 10, 2)->default(0)->after('price_جملة');
            $table->decimal('price_صفحة', 10, 2)->default(0)->after('price_قطاعي');
            $table->integer('volume_ml')->default(100)->after('price_صفحة');
        });

        // Step 2: Set default segment prices from selling_price
        DB::statement('UPDATE products SET price_جملة = selling_price * 0.85');
        DB::statement('UPDATE products SET price_قطاعي = selling_price');
        DB::statement('UPDATE products SET price_صفحة = selling_price * 1.1');
        
        // Step 3: Set random volumes (50ml, 100ml, 150ml, 200ml)
        $volumes = [50, 100, 150, 200];
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            $randomVolume = $volumes[array_rand($volumes)];
            DB::table('products')
                ->where('id', $product->id)
                ->update(['volume_ml' => $randomVolume]);
        }
        
        // Step 4: Rename columns using raw SQL (MariaDB compatible)
        DB::statement('ALTER TABLE products CHANGE stock_quantity quantity INT NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE products CHANGE min_stock_level alert_quantity INT NOT NULL DEFAULT 10');
        DB::statement('ALTER TABLE products CHANGE images photos LONGTEXT NULL');
        
        // Step 5: Drop unused columns
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'category_id',
                'brand_id',
                'cost_price',
                'barcode',
                'reserved_qty',
                'size',
                'image'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'price_جملة',
                'price_قطاعي',
                'price_صفحة',
                'volume_ml',
                'alert_quantity',
                'photos'
            ]);
            
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('supplier')->nullable();
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->string('barcode')->nullable();
        });
    }
};
