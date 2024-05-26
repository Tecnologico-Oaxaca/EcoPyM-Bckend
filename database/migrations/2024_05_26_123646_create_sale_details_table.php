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
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->decimal('price_sale', 6, 2);
            $table->decimal('price_buy', 6, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('discount', 6, 2);
            $table->string('product_id'); 
            $table->unsignedBigInteger('sale_id');
            $table->timestamps();
    
            $table->foreign('product_id')->references('id')->on('products'); 
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};
