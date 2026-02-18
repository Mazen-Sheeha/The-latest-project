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
        // Create the pivot table for many-to-many relationship
        Schema::create('page_pixel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->foreignId('pixel_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate relationships
            $table->unique(['page_id', 'pixel_id']);
        });

        // Drop the pixels column from pages table if it exists
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'pixels')) {
                $table->dropColumn('pixels');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_pixel');

        // Add back the pixels column if rolling back
        Schema::table('pages', function (Blueprint $table) {
            $table->json('pixels')->nullable();
        });
    }
};
