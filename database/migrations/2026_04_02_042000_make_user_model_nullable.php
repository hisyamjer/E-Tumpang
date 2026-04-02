<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('user') || !Schema::hasColumn('user', 'model')) {
            return;
        }

        // Avoid requiring doctrine/dbal for `->change()` by using raw SQL.
        DB::statement('ALTER TABLE `user` MODIFY `model` VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('user') || !Schema::hasColumn('user', 'model')) {
            return;
        }

        DB::statement('ALTER TABLE `user` MODIFY `model` VARCHAR(255) NOT NULL');
    }
};

