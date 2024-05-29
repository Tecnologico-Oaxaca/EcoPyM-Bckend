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
        Schema::create('trends', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('brand');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->decimal('quantity',8, 2);
            $table->string('unit_quantity');
            $table->string('image_url');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trends');
    }
};