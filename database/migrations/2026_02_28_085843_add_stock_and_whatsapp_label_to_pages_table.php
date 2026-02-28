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
            if (!Schema::hasColumn('pages', 'stock')) {
                $table->integer('stock')->nullable();
            }
            if (!Schema::hasColumn('pages', 'whatsapp_label')) {
                $table->string('whatsapp_label')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'stock')) {
                $table->dropColumn('stock');
            }
            if (Schema::hasColumn('pages', 'whatsapp_label')) {
                $table->dropColumn('whatsapp_label');
            }
        });
    }
};
