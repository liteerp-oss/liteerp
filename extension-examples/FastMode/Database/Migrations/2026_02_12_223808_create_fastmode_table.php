<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('FastMode', function (Blueprint $table) {
            $table->id();
            $table->enum('status',['paid','partial_payment','pending'])->default('pending');
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('FastMode');
    }
};