<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->unsignedInteger('sort')->default(0)->after('total_amount');
        });

        DB::table('invoice_items')
            ->orderBy('id')
            ->get(['id', 'invoice_id'])
            ->groupBy('invoice_id')
            ->each(function ($items) {
                $position = 1;
                foreach ($items as $item) {
                    DB::table('invoice_items')
                        ->where('id', $item->id)
                        ->update(['sort' => $position++]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
};
