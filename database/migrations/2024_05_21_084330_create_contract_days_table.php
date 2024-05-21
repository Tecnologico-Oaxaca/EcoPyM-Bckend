<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('contract_days', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('contract_id');
        $table->unsignedBigInteger('day_id');
        $table->boolean('is_work_day');
        $table->timestamps(); 

        $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
        $table->foreign('day_id')->references('id')->on('days')->onDelete('cascade');

        $table->index(['contract_id', 'day_id']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_days');
    }
};
