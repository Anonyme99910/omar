<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for lightning-fast search performance
        Schema::table('sales', function (Blueprint $table) {
            // Index on invoice_number for exact and partial matches
            $table->index('invoice_number', 'idx_sales_invoice_number');
        });
        
        Schema::table('customers', function (Blueprint $table) {
            // Index on name for customer name searches
            $table->index('name', 'idx_customers_name');
            
            // Index on phone for phone number searches
            $table->index('phone', 'idx_customers_phone');
        });
        
        // Add fulltext index for ultra-smart search (MySQL 5.6+)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE customers ADD FULLTEXT INDEX idx_customers_fulltext (name, phone)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('idx_sales_invoice_number');
        });
        
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_customers_name');
            $table->dropIndex('idx_customers_phone');
        });
        
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE customers DROP INDEX idx_customers_fulltext');
        }
    }
};
