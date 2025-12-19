<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("image");
            $table->integer("price");
            $table->string("code");
            $table->integer('stock');
            $table->integer("sales_number")->default(0);
            $table->foreignId("shipping_company_id")->nullable()->constrained()->onDelete("set null");
            $table->boolean("active")->default(true);
            $table->timestamps();
        });
    }
};
