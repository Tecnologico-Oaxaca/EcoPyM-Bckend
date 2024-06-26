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
        Schema::create('product_promotion', function (Blueprint $table) {
            $table->id();
            $table->integer('stock');
            $table->decimal('product_price_buy', 7, 2);
            $table->decimal('product_price_sale', 7, 2);
            $table->string('product_id');
            $table->unsignedBigInteger('promotion_id');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_promotion');
    }
};
