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
        Schema::table('stock_historical_data', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_quantity')->nullable()->after('vwap');
            $table->decimal('delivery_percent', 10, 2)->nullable()->after('delivery_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_historical_data', function (Blueprint $table) {
            $table->dropColumn('delivery_quantity');
            $table->dropColumn('delivery_percent');
        });
    }
};