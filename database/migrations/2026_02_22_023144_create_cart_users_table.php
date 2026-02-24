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
        Schema::create('cart_users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('government')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('set null');
            $table->string('order_index_string');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_users');
    }
};
