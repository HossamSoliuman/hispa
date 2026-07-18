<?php

namespace App\Console\Commands;

use App\Service\DatabaseBackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Throwable;

class DatabaseRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore
        {file? : Path or filename of the backup to restore (defaults to the most recent)}
        {--connection= : The database connection to restore into (defaults to the app default)}
        {--force : Skip the confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore the database from a compressed backup file';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseBackupService $service): int
    {
        $argument = $this->argument('file');

        try {
            $path = $argument !== null
                ? $service->resolveBackupFile($argument)
                : $service->latestBackup();
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($path === null) {
            $this->error('No backup files found in '.$service->backupDirectory());

            return self::FAILURE;
        }

        $connection = $this->option('connection') ?: Config::get('database.default');
        $database = Config::get("database.connections.{$connection}.database");

        $this->line("Connection: {$connection}");
        $this->line("Database:   {$database}");
        $this->line('Backup:     '.$path);

        if (! $this->option('force')) {
            $this->warn("This will PERMANENTLY OVERWRITE the \"{$database}\" database.");

            if (! $this->confirm('Are you sure you want to restore from '.basename($path).'?', false)) {
                $this->info('Restore cancelled.');

                return self::SUCCESS;
            }
        }

        try {
            $result = $service->restore($path, $this->option('connection') ?: null);
        } catch (Throwable $e) {
            Log::error('Database restore failed', ['exception' => $e, 'path' => $path]);
            $this->error('Restore failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info("Restore complete from: {$path}");
        $this->line("Tables: {$result['tables']}, rows: {$result['rows']}, statements executed: {$result['statements']}");

        Log::info('Database restored', $result + ['path' => $path]);

        return self::SUCCESS;
    }
}
