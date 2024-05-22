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
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name', 50)->nullable()->change();
            $table->string('phone', 10)->nullable()->change();  
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name', 50)->nullable(false)->change();
            $table->string('phone', 10)->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
        });
    }
};
