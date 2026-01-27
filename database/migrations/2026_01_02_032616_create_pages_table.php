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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('theme_color')->nullable();
            $table->string(column: 'name');
            $table->string('title');
            $table->integer('items_sold_count');
            $table->integer('reviews_count');
            $table->integer('sale_percent')->nullable();
            $table->decimal('original_price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->date('sale_ends_at')->nullable();
            $table->json('images');
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
