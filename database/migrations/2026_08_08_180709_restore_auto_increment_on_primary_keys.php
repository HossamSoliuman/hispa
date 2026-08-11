<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Restore AUTO_INCREMENT on integer primary keys.
 *
 * A SQL dump imported without the AUTO_INCREMENT attribute leaves every `id`
 * column as a plain NOT NULL primary key, so any insert that does not name an
 * id fails with "Field 'id' doesn't have a default value". Schema-wise the
 * tables still look correct, which is why this hides until a create is
 * attempted.
 *
 * The repair is discovered from information_schema rather than hard-coded, so
 * it is idempotent and skips tables that are already correct or that use a
 * string/UUID primary key by design (sessions, job_batches, notifications).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->columnsMissingAutoIncrement() as $column) {
            $table = $column->TABLE_NAME;
            $type = $column->COLUMN_TYPE;

            $nextId = (int) DB::table($table)->max('id') + 1;

            DB::statement("ALTER TABLE `{$table}` MODIFY `id` {$type} NOT NULL AUTO_INCREMENT");
            DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = {$nextId}");
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: dropping AUTO_INCREMENT again would leave
        // the schema in the broken state this migration exists to repair.
    }

    /**
     * Integer primary keys that should auto-increment but currently do not.
     *
     * @return array<int, object{TABLE_NAME: string, COLUMN_TYPE: string}>
     */
    private function columnsMissingAutoIncrement(): array
    {
        return DB::select(
            "SELECT TABLE_NAME, COLUMN_TYPE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND COLUMN_NAME = 'id'
               AND COLUMN_KEY = 'PRI'
               AND DATA_TYPE IN ('tinyint', 'smallint', 'mediumint', 'int', 'bigint')
               AND EXTRA NOT LIKE '%auto_increment%'
             ORDER BY TABLE_NAME"
        );
    }
};
