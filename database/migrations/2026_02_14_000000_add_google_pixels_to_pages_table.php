<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'google_ads_pixel')) {
                $table->text('google_ads_pixel')->nullable();
            }
            if (!Schema::hasColumn('pages', 'google_analytics')) {
                $table->text('google_analytics')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'google_ads_pixel')) {
                $table->dropColumn('google_ads_pixel');
            }
            if (Schema::hasColumn('pages', 'google_analytics')) {
                $table->dropColumn('google_analytics');
            }
        });
    }
};
