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
        Schema::create('fii_strategies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('symbol_id')->default(0);
            $table->decimal('current_price', 10, 2)->nullable();
            $table->decimal('high_price', 10, 2)->nullable();
            $table->decimal('low_price', 10, 2)->nullable();
            $table->decimal('buy_price', 10, 2)->nullable();
            $table->decimal('sell_price', 10, 2)->nullable();
            $table->enum('status', ['Bought', 'Sold', 'Check', 'Sell Next Day', 'Buy Next Day', 'Hold', 'None'])->default('None');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fii_strategies');
    }
};
