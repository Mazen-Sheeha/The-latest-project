<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\CartUserStatusEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cart_users', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_users', 'status')) {
                $table->string('status')->default(CartUserStatusEnum::PENDING->value);
            }

            if (Schema::hasColumn('cart_users', 'is_completed')) {
                $table->dropColumn('is_completed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_users', function (Blueprint $table) {
            if (Schema::hasColumn('cart_users', 'status')) {
                $table->dropColumn('status');
            }
            if (!Schema::hasColumn('cart_users', 'is_completed')) {
                $table->boolean('is_completed')->default(false);
            }
        });
    }
};
