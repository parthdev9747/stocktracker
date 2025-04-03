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
        Schema::create('market_caps', function (Blueprint $table) {
            $table->id();
            $table->date('time_stamp');
            $table->decimal('market_cap_in_tr_dollars', 10, 2);
            $table->decimal('market_cap_in_lac_cr_rupees', 15, 10);
            $table->decimal('market_cap_in_cr_rupees', 15, 2);
            $table->string('market_cap_in_cr_rupees_formatted');
            $table->string('market_cap_in_lac_cr_rupees_formatted');
            $table->string('underlying');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_caps');
    }
};