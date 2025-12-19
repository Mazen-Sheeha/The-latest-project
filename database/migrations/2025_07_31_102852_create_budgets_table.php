<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->float('budget');
            $table->date('date');
            $table->foreignId("campaign_id")->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
};
