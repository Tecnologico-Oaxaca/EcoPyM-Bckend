<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('phone', 10)->nullable()->change();
            $table->integer('number')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('phone', 10)->nullable(false)->change();
            $table->integer('number')->unsigned()->nullable(false)->change();
        });
    }
};