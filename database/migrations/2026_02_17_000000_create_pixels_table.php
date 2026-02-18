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
        Schema::create('pixels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['meta', 'google_ads', 'google_analytics', 'tiktok', 'snapchat', 'twitter', 'other']);
            $table->string('pixel_id');
            $table->text('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pixels');
    }
};
