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
        Schema::table('invoice_items', function (Blueprint $table) {
            // Remove quantity and unit_price columns
            $table->dropColumn(['quantity', 'unit_price']);

            // Add time tracking columns
            $table->decimal('hours_worked', 8, 2)->after('description');
            $table->decimal('hourly_rate', 10, 2)->after('hours_worked');

            // Rename total_price to total_amount for consistency
            $table->renameColumn('total_price', 'total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Restore product-related columns
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');

            // Restore quantity and unit_price columns
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);

            // Remove time tracking columns
            $table->dropColumn(['hours_worked', 'hourly_rate']);

            // Rename back to total_price
            $table->renameColumn('total_amount', 'total_price');

            $table->index('product_id');
        });
    }
};
