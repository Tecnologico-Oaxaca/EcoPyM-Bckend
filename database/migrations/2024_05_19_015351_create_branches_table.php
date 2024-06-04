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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->text('image')->nullable();
            $table->time('open_time');
            $table->time('close_time');
            $table->string('phone',10)->nullable()->unique();
            $table->string('state');
            $table->string('city');
            $table->string('district');
            $table->string('street');
            $table->integer('number')->nullable();
            $table->unsignedBigInteger('mipyme_id');
            $table->timestamps();

            $table->foreign('mipyme_id')->references('id')->on('mipymes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
