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
        Schema::create('mipyme_business', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mipyme_id');
            $table->unsignedBigInteger('business_id');
            $table->timestamps();

            $table->foreign('mipyme_id')->references('id')->on('mipymes')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('busines')->onDelete('cascade');

            $table->unique(['mipyme_id', 'business_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mipyme_business');
    }
};
