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
        $table->string('name', 200);
        $table->integer('stock')->default(0);
        $table->string('description', 200)->nullable();
        $table->decimal('price_sale', 6, 2)->default(0);
        $table->decimal('price_buy', 6, 2)->default(0);
        $table->text('image')->nullable(); 
        $table->decimal('unit', 6, 2)->nullable();
        $table->boolean('is_active')->default(false);
        $table->unsignedBigInteger('unit_quantity_id')->nullable();
        $table->unsignedBigInteger('business_id');
        $table->unsignedBigInteger('brand_id')->nullable();
        $table->unsignedBigInteger('clasification_id');
        $table->unsignedBigInteger('provider_id')->nullable();
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
