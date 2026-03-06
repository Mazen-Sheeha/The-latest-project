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
        Schema::table('cart_users', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_users', 'utm_source')) {
                $table->string('utm_source')->nullable();
            }
            if (!Schema::hasColumn('cart_users', 'utm_campaign')) {
                $table->string('utm_campaign')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_users', function (Blueprint $table) {
            if (Schema::hasColumn('cart_users', 'utm_source')) {
                $table->dropColumn('utm_source');
            }
            if (Schema::hasColumn('cart_users', 'utm_campaign')) {
                $table->dropColumn('utm_campaign');
            }
        });
    }
};
