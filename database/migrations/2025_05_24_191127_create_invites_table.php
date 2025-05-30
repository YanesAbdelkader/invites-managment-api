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
        Schema::create('invites', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');           
            $table->string('last_name');
            $table->string('description');
            $table->string('phone')->nullable();
            $table->enum('presence', ['حاضر', 'غائب', 'لم يتم التسجيل'])->default('لم يتم التسجيل');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};
