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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('business')->onDelete('cascade');
            $table->string('name');                         
            $table->string('contact_name')->nullable();     
            $table->string('email')->nullable();          
            $table->string('phone')->nullable();        
            $table->string('address')->nullable();         
            $table->string('tax_code')->nullable();
            $table->string('national_id')->nullable();       
            $table->string('bank_name')->nullable();      
            $table->string('bank_account')->nullable();   
            $table->enum('type', ['individual', 'company'])->default('individual');
            $table->foreignId('group')->constrained('customer_group')->onDelete('cascade'); 
            $table->string('website')->nullable();
            $table->text('note')->nullable();
            $table->boolean('active')->default(true);        
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};
