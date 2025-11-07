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
        Schema::table('sales', function (Blueprint $table) {
            // Change status enum to support invoice statuses
            $table->dropColumn('status');
        });
        
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('status', ['draft', 'issued', 'partially_paid', 'paid', 'void'])->default('issued')->after('payment_method');
            $table->decimal('shipping', 10, 2)->default(0)->after('discount');
            $table->decimal('paid_sum', 10, 2)->default(0)->after('total');
            $table->decimal('balance_due', 10, 2)->default(0)->after('paid_sum');
            $table->date('issue_date')->nullable()->after('invoice_number');
            $table->date('due_date')->nullable()->after('issue_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->index('status');
            $table->index('issue_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['shipping', 'paid_sum', 'balance_due', 'issue_date', 'due_date', 'created_by']);
            $table->dropIndex(['sales_status_index', 'sales_issue_date_index']);
            $table->dropColumn('status');
        });
        
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
        });
    }
};
