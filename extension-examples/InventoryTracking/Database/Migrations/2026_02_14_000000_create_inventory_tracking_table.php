<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_tracking', function (Blueprint $table) {
            $table->id();
            $table->decimal('min', 15, 2)->default(0);
            $table->foreignId('business_id')->constrained('business')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_tracking');
    }
};
