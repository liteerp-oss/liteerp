<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('stock_movements_in_id')->constrained('stock_movements_in')->onDelete('cascade');
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('buy_quantity',15,2)->default(0);
            $table->decimal('gift_quantity',15,2)->default(0);
            $table->decimal('compensation_quantity',15,2)->default(0);
            $table->decimal('conversion_quantity',15,2)->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->boolean('cancelled')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
