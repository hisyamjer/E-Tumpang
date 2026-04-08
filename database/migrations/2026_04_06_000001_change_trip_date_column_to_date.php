<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('trip') || !Schema::hasColumn('trip', 'date')) {
            return;
        }

        $dateColumn = DB::selectOne("
            SELECT DATA_TYPE AS data_type
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'trip'
              AND COLUMN_NAME = 'date'
            LIMIT 1
        ");

        if ($dateColumn && strtolower((string) $dateColumn->data_type) === 'date') {
            return;
        }

        if (!Schema::hasColumn('trip', 'date_new')) {
            DB::statement("ALTER TABLE `trip` ADD COLUMN `date_new` DATE NULL AFTER `departure_time`");
        }

        DB::statement("
            UPDATE `trip`
            SET `date_new` = STR_TO_DATE(`date`, '%Y-%m-%d')
            WHERE `date` IS NOT NULL AND `date` != '' AND `date_new` IS NULL
        ");

        DB::statement("ALTER TABLE `trip` DROP COLUMN `date`");
        DB::statement("ALTER TABLE `trip` CHANGE `date_new` `date` DATE NULL");
    }

    public function down(): void
    {
        if (!Schema::hasTable('trip') || !Schema::hasColumn('trip', 'date')) {
            return;
        }

        $dateColumn = DB::selectOne("
            SELECT DATA_TYPE AS data_type
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'trip'
              AND COLUMN_NAME = 'date'
            LIMIT 1
        ");

        if ($dateColumn && strtolower((string) $dateColumn->data_type) === 'varchar') {
            return;
        }

        if (!Schema::hasColumn('trip', 'date_old')) {
            DB::statement("ALTER TABLE `trip` ADD COLUMN `date_old` VARCHAR(255) NULL AFTER `departure_time`");
        }

        DB::statement("
            UPDATE `trip`
            SET `date_old` = DATE_FORMAT(`date`, '%Y-%m-%d')
            WHERE `date` IS NOT NULL AND `date_old` IS NULL
        ");

        DB::statement("ALTER TABLE `trip` DROP COLUMN `date`");
        DB::statement("ALTER TABLE `trip` CHANGE `date_old` `date` VARCHAR(255) NULL");
    }
};

