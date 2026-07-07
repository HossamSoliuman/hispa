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
     * Dump a MySQL/MariaDB database's data (INSERT rows only) to a gzipped SQL
     * file using the app's own PDO connection, so no external mysqldump binary
     * or shell access is needed.
     */
    protected function backupMysql(string $connection, string $directory, string $timestamp): string
    {
        $db = DB::connection($connection);
        $database = $db->getDatabaseName();
        $path = "{$directory}/{$database}_{$timestamp}.sql.gz";

        $handle = gzopen($path, 'wb9');
        if ($handle === false) {
            throw new \RuntimeException("Unable to open backup file for writing: {$path}");
        }

        $pdo = $db->getPdo();
        $bufferedByDefault = $pdo->getAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY);
        $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

        try {
            gzwrite($handle, "-- Data-only backup of `{$database}` at ".Carbon::now()->toDateTimeString()."\n");
            gzwrite($handle, "SET NAMES utf8mb4;\n");
            gzwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            foreach ($this->baseTables($db) as $table) {
                $this->dumpTableData($pdo, $handle, $table);
            }

            gzwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        } catch (\Throwable $e) {
            gzclose($handle);
            File::delete($path);
            throw $e;
        } finally {
            $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $bufferedByDefault);
        }

        gzclose($handle);

        return $path;
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
     * Write an INSERT statement for every row of a single table.
     *
     * @param  resource  $handle
     */
    protected function dumpTableData(\PDO $pdo, $handle, string $table): void
    {
        $statement = $pdo->query('SELECT * FROM `'.$table.'`');
        $columns = null;

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            if ($columns === null) {
                $columns = '`'.implode('`, `', array_keys($row)).'`';
            }

            $values = implode(', ', array_map(
                fn ($value): string => $value === null ? 'NULL' : $pdo->quote((string) $value),
                array_values($row)
            ));

            gzwrite($handle, "INSERT INTO `{$table}` ({$columns}) VALUES ({$values});\n");
        }
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
