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
        Schema::create('indicative_nifty50s', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_time');
            $table->dateTime('indicative_time')->nullable();
            $table->string('index_name');
            $table->decimal('index_last', 10, 2)->nullable();
            $table->decimal('index_perc_change', 8, 2)->nullable();
            $table->string('index_time_val')->nullable();
            $table->decimal('closing_value', 10, 2)->nullable();
            $table->decimal('final_closing_value', 10, 2)->nullable();
            $table->decimal('change', 10, 2)->nullable();
            $table->decimal('per_change', 8, 2)->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicative_nifty50s');
    }
};