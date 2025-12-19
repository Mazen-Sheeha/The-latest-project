<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string("url");
            $table->string("campaign");
            $table->string("source");
            $table->foreignId("adset_id");
            $table->boolean("active")->default(true);
            $table->timestamps();
        });
    }
};
