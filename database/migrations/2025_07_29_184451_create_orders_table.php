<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string("url")->nullable();
            $table->string("name");
            $table->string("phone");
            $table->string("city")->nullable();
            $table->string("address");
            $table->decimal("shipping_price", 10, 3);
            $table->enum('order_status', [
                'waiting_for_confirmation',
                'waiting_for_shipping',
                'received',
                'sent',
                'postponed',
                'no_response',
                'exchanged',
                'rejected_with_phone',
                'rejected_in_shipping'
            ])->default('waiting_for_confirmation');
            $table->foreignId('campaign_id')->nullable()->constrained()->onDelete("set null");
            $table->boolean('paid')->default(false);
            $table->string("tracking_number")->nullable();
            $table->string("ref")->nullable();
            $table->date("date_of_postponement")->nullable();
            $table->timestamps();
        });
    }
};
