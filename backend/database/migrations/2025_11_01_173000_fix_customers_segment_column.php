<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, update any invalid segments to default
        DB::table('customers')
            ->whereNotIn('segment', ['جملة', 'قطاعي', 'صفحة'])
            ->update(['segment' => 'قطاعي']);
        
        // Drop and recreate with proper constraints
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('segment');
        });
        
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('segment', ['جملة', 'قطاعي', 'صفحة'])
                  ->default('قطاعي')
                  ->after('address');
        });
        
        // Ensure phone is not nullable and has unique index
        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone', 20)->nullable(false)->change();
        });
        
        // Add unique index on phone if not exists
        if (!Schema::hasColumn('customers', 'phone')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->unique('phone');
            });
        }
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['phone']);
        });
    }
};
