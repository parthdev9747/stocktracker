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
        Schema::create('stock_historical_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('symbol_id')->default(0);
            $table->string('series', 5)->nullable();
            $table->string('market_type', 5)->nullable();
            $table->date('trade_date')->index();
            $table->decimal('high_price', 15, 2)->nullable();
            $table->decimal('low_price', 15, 2)->nullable();
            $table->decimal('opening_price', 15, 2)->nullable();
            $table->decimal('closing_price', 15, 2)->nullable();
            $table->decimal('last_traded_price', 15, 2)->nullable();
            $table->decimal('previous_close_price', 15, 2)->nullable();
            $table->bigInteger('traded_quantity')->nullable();
            $table->decimal('traded_value', 20, 2)->nullable();
            $table->decimal('week_high_52', 15, 2)->nullable();
            $table->decimal('week_low_52', 15, 2)->nullable();
            $table->integer('total_trades')->nullable();
            $table->string('isin', 20)->nullable();
            $table->decimal('vwap', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_historical_data');
    }
};
