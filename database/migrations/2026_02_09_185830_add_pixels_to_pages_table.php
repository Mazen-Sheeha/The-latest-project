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
            if (!Schema::hasColumn('pages', 'meta_pixel')) {
                $table->text('meta_pixel')->nullable();
            }
            if (!Schema::hasColumn('pages', 'tiktok_pixel')) {
                $table->text('tiktok_pixel')->nullable();
            }
            if (!Schema::hasColumn('pages', 'snapchat_pixel')) {
                $table->text('snapchat_pixel')->nullable();
            }
            if (!Schema::hasColumn('pages', 'twitter_pixel')) {
                $table->text('twitter_pixel')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'meta_pixel')) {
                $table->dropColumn('meta_pixel');
            }

            if (Schema::hasColumn('pages', 'tiktok_pixel')) {
                $table->dropColumn('tiktok_pixel');
            }

            if (Schema::hasColumn('pages', 'snapchat_pixel')) {
                $table->dropColumn('snapchat_pixel');
            }

            if (Schema::hasColumn('pages', 'twitter_pixel')) {
                $table->dropColumn('twitter_pixel');
            }
        });
    }
};
