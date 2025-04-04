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
        Schema::table('pre_open_market_data', function (Blueprint $table) {
            $table->boolean('is_slb')->default(false)->after('is_fno');
            $table->boolean('is_etf')->default(false)->after('is_slb');
            $table->boolean('is_suspended')->default(false)->after('is_etf');
            $table->boolean('is_delisted')->default(false)->after('is_suspended');
            $table->string('isin')->nullable()->after('is_delisted');
            $table->date('listing_date')->nullable()->after('isin');
            $table->string('industry')->nullable()->after('listing_date');
            $table->float('face_value')->nullable()->after('industry');
            $table->bigInteger('issued_size')->nullable()->after('face_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_open_market_data', function (Blueprint $table) {
            $table->dropColumn([
                'is_fno',
                'is_slb',
                'is_etf',
                'is_suspended',
                'is_delisted',
                'isin',
                'listing_date',
                'industry',
                'face_value',
                'issued_size',
            ]);
        });
    }
};
