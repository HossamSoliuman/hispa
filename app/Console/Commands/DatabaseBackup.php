<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup
        {--keep-days=7 : Delete backups older than this many days (0 keeps all)}
        {--connection= : The database connection to back up (defaults to the app default)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a compressed backup of the database and prune old ones';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $connection = $this->option('connection') ?: Config::get('database.default');
        $driver = Config::get("database.connections.{$connection}.driver");

        if ($driver === null) {
            $this->error("Database connection [{$connection}] is not configured.");

            return self::FAILURE;
        }

        $directory = storage_path('app/backups');
        File::ensureDirectoryExists($directory);

        $timestamp = Carbon::now()->format('Y-m-d_His');

        try {
            $path = match ($driver) {
                'mysql', 'mariadb' => $this->backupMysql($connection, $directory, $timestamp),
                'sqlite' => $this->backupSqlite($connection, $directory, $timestamp),
                default => throw new \RuntimeException("Unsupported database driver [{$driver}] for backup."),
            };
        } catch (\Throwable $e) {
            Log::error('Database backup failed', ['exception' => $e]);
            $this->error('Backup failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $size = $this->humanFileSize(File::size($path));
        $this->info("Backup created: {$path} ({$size})");
        Log::info('Database backup created', ['path' => $path, 'size' => $size]);

        $this->pruneOldBackups($directory);

        return self::SUCCESS;
    }

    /**
     * Dump a MySQL/MariaDB database's data (INSERT rows only, no schema) to a
     * gzipped SQL file using the app's own PDO connection, so no external
     * mysqldump binary or shell access is needed.
     *
     * The reads run inside a single consistent snapshot so the row counts used
     * to verify the dump match the rows actually written. If any table comes up
     * short the file is deleted and the command fails loudly, so a truncated or
     * empty backup can never be mistaken for a good one.
     */
    protected function backupMysql(string $connection, string $directory, string $timestamp): string
    {
        $db = DB::connection($connection);
        $database = $db->getDatabaseName();
        $path = "{$directory}/{$database}_{$timestamp}.sql.gz";
        $tables = $this->baseTables($db);

        $handle = gzopen($path, 'wb9');
        if ($handle === false) {
            throw new \RuntimeException("Unable to open backup file for writing: {$path}");
        }

        $pdo = $db->getPdo();
        $bufferedByDefault = $pdo->getAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY);

        try {
            $pdo->exec('START TRANSACTION WITH CONSISTENT SNAPSHOT');

            $expected = $this->rowCounts($pdo, $tables);

            gzwrite($handle, "-- Data-only backup of `{$database}` at ".Carbon::now()->toDateTimeString()."\n");
            gzwrite($handle, "SET NAMES utf8mb4;\n");
            gzwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            gzwrite($handle, "-- Clear existing data so a single import fully restores the database\n");
            foreach ($tables as $table) {
                gzwrite($handle, "TRUNCATE TABLE `{$table}`;\n");
            }
            gzwrite($handle, "\n");

            $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

            $written = [];
            foreach ($tables as $table) {
                $written[$table] = $this->dumpTableData($pdo, $handle, $table);
            }

            $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $bufferedByDefault);

            gzwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");

            $pdo->exec('COMMIT');

            $this->assertBackupComplete($expected, $written);
        } catch (\Throwable $e) {
            $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $bufferedByDefault);
            $this->rollBackQuietly($pdo);
            gzclose($handle);
            File::delete($path);
            throw $e;
        }

        gzclose($handle);

        return $path;
    }

    /**
     * Count the rows of every table within the current snapshot.
     *
     * @param  list<string>  $tables
     * @return array<string, int>
     */
    protected function rowCounts(\PDO $pdo, array $tables): array
    {
        $counts = [];

        foreach ($tables as $table) {
            $counts[$table] = (int) $pdo->query('SELECT COUNT(*) FROM `'.$table.'`')->fetchColumn();
        }

        return $counts;
    }

    /**
     * Fail if any table wrote fewer rows than it holds, deleting a partial dump.
     *
     * @param  array<string, int>  $expected
     * @param  array<string, int>  $written
     */
    protected function assertBackupComplete(array $expected, array $written): void
    {
        $mismatches = [];

        foreach ($expected as $table => $count) {
            if (($written[$table] ?? 0) !== $count) {
                $mismatches[] = "{$table} (expected {$count}, wrote ".($written[$table] ?? 0).')';
            }
        }

        if ($mismatches !== []) {
            throw new \RuntimeException('Backup is incomplete; row counts do not match for: '.implode(', ', $mismatches));
        }
    }

    /**
     * Roll back the snapshot transaction without masking the original error.
     */
    protected function rollBackQuietly(\PDO $pdo): void
    {
        try {
            $pdo->exec('ROLLBACK');
        } catch (\Throwable) {
            // The connection is script-scoped and closes with the command.
        }
    }

    /**
     * List the base tables in the database, skipping views (which hold no data).
     *
     * @return list<string>
     */
    protected function baseTables(Connection $db): array
    {
        $tables = [];

        foreach ($db->select('SHOW FULL TABLES') as $row) {
            $values = array_values((array) $row);

            if (($values[1] ?? 'BASE TABLE') !== 'VIEW') {
                $tables[] = $values[0];
            }
        }

        return $tables;
    }

    /**
     * Stream a single table's rows into batched multi-row INSERT statements and
     * return how many rows were written. Rows are flushed once a batch grows
     * past ~500 KB so restores stay well under the server's max_allowed_packet.
     *
     * @param  resource  $handle
     */
    protected function dumpTableData(\PDO $pdo, $handle, string $table): int
    {
        $statement = $pdo->query('SELECT * FROM `'.$table.'`');
        $columns = null;
        $tuples = [];
        $bytes = 0;
        $written = 0;

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            if ($columns === null) {
                $columns = '`'.implode('`, `', array_keys($row)).'`';
            }

            $tuple = '('.implode(', ', array_map(
                fn ($value): string => $value === null ? 'NULL' : $pdo->quote((string) $value),
                array_values($row)
            )).')';

            $tuples[] = $tuple;
            $bytes += strlen($tuple);
            $written++;

            if ($bytes >= 500_000) {
                $this->writeInsert($handle, $table, $columns, $tuples);
                $tuples = [];
                $bytes = 0;
            }
        }

        if ($tuples !== []) {
            $this->writeInsert($handle, $table, $columns, $tuples);
        }

        return $written;
    }

    /**
     * Write one multi-row INSERT statement for a batch of value tuples.
     *
     * @param  resource  $handle
     * @param  list<string>  $tuples
     */
    protected function writeInsert($handle, string $table, string $columns, array $tuples): void
    {
        gzwrite($handle, "INSERT INTO `{$table}` ({$columns}) VALUES\n".implode(",\n", $tuples).";\n");
    }

    /**
     * Copy and gzip a SQLite database file.
     */
    protected function backupSqlite(string $connection, string $directory, string $timestamp): string
    {
        $database = Config::get("database.connections.{$connection}.database");

        if (! File::exists($database)) {
            throw new \RuntimeException("SQLite database file not found: {$database}");
        }

        $name = pathinfo($database, PATHINFO_FILENAME);
        $path = "{$directory}/{$name}_{$timestamp}.sqlite.gz";

        $source = fopen($database, 'rb');
        $handle = gzopen($path, 'wb9');

        if ($source === false || $handle === false) {
            throw new \RuntimeException('Unable to open files for SQLite backup.');
        }

        while (! feof($source)) {
            gzwrite($handle, fread($source, 1024 * 512));
        }

        fclose($source);
        gzclose($handle);

        return $path;
    }

    /**
     * Delete backups older than the retention window.
     */
    protected function pruneOldBackups(string $directory): void
    {
        $keepDays = (int) $this->option('keep-days');

        if ($keepDays <= 0) {
            return;
        }

        $cutoff = Carbon::now()->subDays($keepDays);
        $deleted = 0;

        foreach (File::files($directory) as $file) {
            if (Carbon::createFromTimestamp($file->getMTime())->lessThan($cutoff)) {
                File::delete($file->getPathname());
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("Pruned {$deleted} backup(s) older than {$keepDays} day(s).");
        }
    }

    /**
     * Format a byte count into a human-readable string.
     */
    protected function humanFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $bytes > 0 ? (int) floor(log($bytes, 1024)) : 0;
        $power = min($power, count($units) - 1);

        return round($bytes / (1024 ** $power), 2).' '.$units[$power];
    }
}
