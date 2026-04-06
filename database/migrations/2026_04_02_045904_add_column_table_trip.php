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
        Schema::table('trip', function (Blueprint $table) {
        $table->string('car_model')->nullable()->after('studentID');
        $table->string('plate_number')->nullable()->after('car_model');

        // Trip specifics (driver's input)
        $table->text('description')->nullable()->after('destination'); 
        $table->string('gender_pref')->default('Mixed')->after('available_seats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip', function (Blueprint $table) {
            $table->dropColumn(['car_model', 'plate_number', 'description', 'gender_pref']);
        });
    }
};
