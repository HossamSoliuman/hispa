<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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
     * Dump a MySQL/MariaDB database and gzip it in a single streaming pipeline.
     */
    protected function backupMysql(string $connection, string $directory, string $timestamp): string
    {
        $config = Config::get("database.connections.{$connection}");
        $database = $config['database'];
        $path = "{$directory}/{$database}_{$timestamp}.sql.gz";

        $dumpBinary = Config::get('database.dump.mysqldump_binary', 'mysqldump');

        $command = [
            $dumpBinary,
            '--host='.($config['host'] ?? '127.0.0.1'),
            '--port='.($config['port'] ?? '3306'),
            '--user='.($config['username'] ?? 'root'),
            '--single-transaction',
            '--quick',
            '--default-character-set='.($config['charset'] ?? 'utf8mb4'),
            $database,
        ];

        $env = [];
        if (! empty($config['password'])) {
            $env['MYSQL_PWD'] = $config['password'];
        }

        $dump = new Process($command, null, $env ?: null, null, 3600);
        $dump->start();

        $handle = gzopen($path, 'wb9');
        if ($handle === false) {
            $dump->stop();
            throw new \RuntimeException("Unable to open backup file for writing: {$path}");
        }

        foreach ($dump as $type => $data) {
            if ($type === Process::OUT) {
                gzwrite($handle, $data);
            }
        }

        gzclose($handle);

        if (! $dump->isSuccessful()) {
            File::delete($path);
            throw new ProcessFailedException($dump);
        }

        return $path;
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
