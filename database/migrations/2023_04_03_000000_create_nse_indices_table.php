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
        Schema::create('nse_indices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->boolean('is_derivative_eligible')->default(false);
            $table->timestamps();
            
            // Add unique constraint to prevent duplicates
            $table->unique(['name', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nse_indices');
    }
};