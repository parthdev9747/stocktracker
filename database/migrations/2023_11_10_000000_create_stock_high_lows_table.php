<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_high_lows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('symbol_id');
            $table->date('trade_date');
            $table->boolean('is_high')->default(false);
            $table->boolean('is_low')->default(false);
            $table->decimal('current_high', 10, 2);
            $table->decimal('current_low', 10, 2);
            $table->decimal('period_high', 10, 2);
            $table->decimal('period_low', 10, 2);
            $table->integer('period_days');
            $table->timestamps();
            
            $table->foreign('symbol_id')->references('id')->on('pre_open_market_data')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_high_lows');
    }
};