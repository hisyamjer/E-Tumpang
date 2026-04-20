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
        Schema::create('user', function (Blueprint $table) {
            $table->id('userID');
            $table->unsignedBigInteger('studentID');
            $table->foreign('studentID')->references('studentID')->on('student')->onDelete('cascade');
            $table->string('role')->enum('passenger', 'driver', 'admin');
            $table->string('plate_number')->nullable();
            $table->string('password');
            $table->string('model')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
