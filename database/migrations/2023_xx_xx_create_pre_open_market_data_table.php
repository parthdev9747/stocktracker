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
        Schema::create('pre_open_market_data', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->index();
            $table->boolean('is_fno')->default(false);
            $table->string('status')->default('active');
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('change', 15, 2)->default(0);
            $table->decimal('percent_change', 15, 2)->default(0);
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_open_market_data');
    }
};
