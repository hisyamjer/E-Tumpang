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
    Schema::table('user', function (Blueprint $table) {
    $table->boolean('is_default')->default(false)->after('plate_number');
    $table->string('model')->nullable()->after('plate_number');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
