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
        Schema::create('trip', function (Blueprint $table) {
            $table->id('tripID');
            $table->unsignedBigInteger('studentID');
            $table->foreign('studentID')->references('studentID')->on('student')->onDelete('cascade');
            $table->string('destination');
            $table->time('departure_time');
            $table->date('date');
            $table->integer('available_seats');
            $table->double('price', 10, 2);
            $table->string('status')->enum('arrived', 'confirmed', 'completed', 'cancelled');
            $table->timestamp('created_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip');
    }
};
