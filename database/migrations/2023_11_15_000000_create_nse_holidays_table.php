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
        Schema::create('nse_holidays', function (Blueprint $table) {
            $table->id();
            $table->date('trading_date');
            $table->string('day');
            $table->string('description');
            $table->string('market_segment');
            $table->string('exchange')->default('NSE');
            $table->string('year');
            $table->timestamps();
            
            // Add unique constraint to prevent duplicates
            $table->unique(['trading_date', 'market_segment']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nse_holidays');
    }
};