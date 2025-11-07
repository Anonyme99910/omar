<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->boolean('with_packaging')->default(false)->after('total_price');
            $table->integer('packaging_quantity')->nullable()->after('with_packaging');
            $table->decimal('packaging_price', 10, 2)->nullable()->after('packaging_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['with_packaging', 'packaging_quantity', 'packaging_price']);
        });
    }
};
