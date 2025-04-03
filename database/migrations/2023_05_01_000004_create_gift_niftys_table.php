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
        Schema::create('gift_niftys', function (Blueprint $table) {
            $table->id();
            $table->string('instrument_type');
            $table->string('symbol');
            $table->string('expiry_date');
            $table->string('option_type')->nullable();
            $table->string('strike_price')->nullable();
            $table->decimal('last_price', 10, 2);
            $table->string('day_change');
            $table->string('per_change');
            $table->integer('contracts_traded');
            $table->dateTime('timestamp');
            $table->string('external_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_niftys');
    }
};