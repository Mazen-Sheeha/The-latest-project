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
        Schema::table('page_upsell_products', function (Blueprint $table) {
            $table->string('name')->nullable()->after('product_id');
            $table->string('image')->nullable()->after('name');
            $table->decimal('price', 10, 2)->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_upsell_products', function (Blueprint $table) {
            $table->dropColumn(['name', 'image', 'price']);
        });
    }
};
