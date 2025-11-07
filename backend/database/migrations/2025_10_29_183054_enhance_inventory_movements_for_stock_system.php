<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->enum('type', ['sale', 'return', 'manual_adjust', 'purchase', 'reserve', 'release'])->after('product_id');
            $table->string('related_type')->nullable()->after('quantity');
            $table->unsignedBigInteger('related_id')->nullable()->after('related_type');
            $table->datetime('moved_at')->nullable()->after('related_id');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->index('moved_at');
            $table->index(['related_type', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn(['related_type', 'related_id', 'moved_at', 'created_by']);
            $table->dropIndex(['inventory_movements_moved_at_index']);
            $table->dropIndex(['inventory_movements_related_type_related_id_index']);
            $table->dropColumn('type');
        });
        
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->enum('type', ['in', 'out', 'adjustment']);
        });
    }
};
