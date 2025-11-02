<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Change volume_ml from integer to string for manual input
            $table->string('volume_ml', 50)->nullable()->change();
            
            // Make name (English) nullable since we're removing it from required fields
            $table->string('name')->nullable()->change();
            
            // Make sku nullable
            $table->string('sku')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('volume_ml')->change();
            $table->string('name')->change();
            $table->string('sku')->unique()->change();
        });
    }
};
