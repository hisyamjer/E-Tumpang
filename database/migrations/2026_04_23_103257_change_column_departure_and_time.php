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
    $table->datetime('departure_at')->after('destination'); 
    $table->dropColumn(['date', 'departure_time']); 
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::table('trip', function (Blueprint $table) {
    $table->date('date')->after('destination');
    $table->time('departure_time')->after('date');
    $table->dropColumn('departure_at');
    });
}
};
