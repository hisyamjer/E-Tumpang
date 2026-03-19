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
        Schema::create('booking', function (Blueprint $table) {
            $table->id('bookingID')->primary();
            $table->unsignedBigInteger('tripID');
            $table->foreign('tripID')->references('tripID')->on('trip')->onDelete('cascade');
            $table->unsignedBigInteger('studentID');
            $table->foreign('studentID')->references('studentID')->on('student')->onDelete('cascade');
            $table->integer('seats_booked');
            $table->string('status')->enum( 'confirmed', 'cancelled');
            $table->datetime('booking_date')->nullable();
        });

        Schema::create('feedback', function (Blueprint $table) {
            $table->id('feedbackID')->primary();
            $table->unsignedBigInteger('tripID');
            $table->foreign('tripID')->references('tripID')->on('trip')->onDelete('cascade');
            $table->unsignedBigInteger('studentID');
            $table->foreign('studentID')->references('studentID')->on('student')->onDelete('cascade');
            $table->integer('rating')->unsigned()->nullable();
            $table->text('comment')->nullable();
            $table->datetime('feedback_date')->nullable();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
        Schema::dropIfExists('feedback');
    }
};
