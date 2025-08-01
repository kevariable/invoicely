<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip migration - currency field already exists as string which works fine
        // We'll just update the form options to limit to USD/GBP
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse
    }
};
