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
        Schema::create('market_index_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('symbol_id')->default(0);
            $table->decimal('open', 10, 2)->default(0);
            $table->decimal('high', 10, 2)->default(0);
            $table->decimal('low', 10, 2)->default(0);
            $table->decimal('close', 10, 2)->default(0);
            $table->bigInteger('volume')->default(0);
            $table->string('time_frame')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_index_data');
    }
};
