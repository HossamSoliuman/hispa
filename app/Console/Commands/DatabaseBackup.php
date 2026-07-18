<?php

namespace App\Console\Commands;

use App\Service\DatabaseBackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

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
    protected $description = 'Create a compressed, self-contained backup of the database and prune old ones';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseBackupService $service): int
    {
        try {
            $result = $service->backup($this->option('connection') ?: null, (int) $this->option('keep-days'));
        } catch (Throwable $e) {
            Log::error('Database backup failed', ['exception' => $e]);
            $this->error('Backup failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $size = $this->humanFileSize($result['size']);
        $this->info("Backup created: {$result['path']} ({$size})");
        $this->line("Tables: {$result['tables']}, rows: {$result['rows']}");

        if ($result['pruned'] > 0) {
            $this->info("Pruned {$result['pruned']} old backup(s).");
        }

        Log::info('Database backup created', [
            'path' => $result['path'],
            'size' => $size,
            'tables' => $result['tables'],
            'rows' => $result['rows'],
        ]);

        return self::SUCCESS;
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
