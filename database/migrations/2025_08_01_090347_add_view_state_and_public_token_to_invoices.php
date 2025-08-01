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
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('view_state', ['unread', 'viewed'])->default('unread')->after('status');
            $table->string('public_token', 64)->unique()->nullable()->after('view_state');
            $table->timestamp('viewed_at')->nullable()->after('public_token');
            
            $table->index('view_state');
            $table->index('public_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['view_state']);
            $table->dropIndex(['public_token']);
            $table->dropColumn(['view_state', 'public_token', 'viewed_at']);
        });
    }
};
