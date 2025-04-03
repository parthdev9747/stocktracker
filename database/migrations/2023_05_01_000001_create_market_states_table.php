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
        Schema::create('market_states', function (Blueprint $table) {
            $table->id();
            $table->string('market');
            $table->string('market_status');
            $table->dateTime('trade_date')->nullable();
            $table->string('index')->nullable();
            $table->string('last')->nullable();
            $table->string('variation')->nullable();
            $table->decimal('percent_change', 8, 2)->nullable();
            $table->string('market_status_message')->nullable();
            $table->string('expiry_date')->nullable();
            $table->string('underlying')->nullable();
            $table->dateTime('updated_time')->nullable();
            $table->string('slick_class')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_states');
    }
};