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
        Schema::create('index_names', function (Blueprint $table) {
            $table->id();
            $table->string('index_name')->nullable();
            $table->string('index_code')->nullable()->index();
            $table->string('index_url')->nullable();
            $table->string('index_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('index_names');
    }
};