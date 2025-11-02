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
        Schema::table('customers', function (Blueprint $table) {
            // Add encrypted columns
            $table->text('phone_encrypted')->nullable()->after('phone');
            $table->text('address_encrypted')->nullable()->after('address');
            
            // Add hash columns for searching
            $table->string('phone_hash', 64)->nullable()->after('phone_encrypted')->index();
            $table->string('address_hash', 64)->nullable()->after('address_encrypted')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['phone_encrypted', 'address_encrypted', 'phone_hash', 'address_hash']);
        });
    }
};
