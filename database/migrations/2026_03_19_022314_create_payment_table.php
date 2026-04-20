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
        Schema::create('payment', function (Blueprint $table) {
            $table->string('paymentID');
            $table->unsignedBigInteger('bookingID');
            $table->foreign('bookingID')->references('bookingID')->on('booking')->onDelete('cascade');
            $table->double('amount', 10, 2);
            $table->string('payment_method')->enum('credit_card', 'online_banking');
            $table->string('status')->enum('pending', 'completed', 'failed');
            $table->datetime('payment_date')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
