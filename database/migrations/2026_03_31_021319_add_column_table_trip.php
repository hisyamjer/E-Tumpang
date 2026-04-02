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
    // 1. Handle the Rename (Defensive)
    if (!Schema::hasColumn('trip', 'date')) {
        $table->string('date')->after('departure_time')->nullable(); 
    }
    });
    }
        
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
