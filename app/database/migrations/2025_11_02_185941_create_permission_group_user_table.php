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
        Schema::create('permission_group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('permission_groups')->onDelete('cascade');
            $table->timestamps();
            $table->foreignId('account_id')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_group_user');
    }
};
