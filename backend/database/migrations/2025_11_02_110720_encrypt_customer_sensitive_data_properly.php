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
        // Step 1: Change phone and address columns to TEXT to store encrypted data
        Schema::table('customers', function (Blueprint $table) {
            $table->text('phone')->nullable()->change();
            $table->text('address')->nullable()->change();
        });
        
        // Step 2: Add hash columns for searching
        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone_hash', 64)->nullable()->after('phone')->index();
            $table->string('address_hash', 64)->nullable()->after('address')->index();
        });
        
        // Step 3: Drop the temporary encrypted columns if they exist
        if (Schema::hasColumn('customers', 'phone_encrypted')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn(['phone_encrypted', 'address_encrypted']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['phone_hash', 'address_hash']);
            $table->string('phone', 20)->nullable()->change();
            $table->string('address', 255)->nullable()->change();
        });
    }
};
