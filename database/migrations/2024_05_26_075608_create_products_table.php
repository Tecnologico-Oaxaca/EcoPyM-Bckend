<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
    Schema::create('products', function (Blueprint $table) {
        $table->string('id')->primary();
        $table->string('name', 45);
        $table->integer('stock');
        $table->string('description', 200)->nullable();
        $table->decimal('price_sale', 6, 2);
        $table->decimal('price_buy', 6, 2);
        $table->text('image')->nullable(); 
        $table->decimal('unit', 6, 2);
        $table->unsignedBigInteger('unit_quantity_id');
        $table->unsignedBigInteger('business_id');
        $table->unsignedBigInteger('brand_id');
        $table->unsignedBigInteger('clasification_id');
        $table->unsignedBigInteger('provider_id');
        $table->timestamps();

        $table->foreign('unit_quantity_id')->references('id')->on('unit_quantities');
        $table->foreign('business_id')->references('id')->on('busines'); 
        $table->foreign('brand_id')->references('id')->on('brands');
        $table->foreign('clasification_id')->references('id')->on('clasifications');
        $table->foreign('provider_id')->references('id')->on('providers');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
