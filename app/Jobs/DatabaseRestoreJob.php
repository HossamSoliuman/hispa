<?php

namespace App\Jobs;

use App\Service\DatabaseBackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DatabaseRestoreJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted. A restore is destructive
     * and must never be retried automatically.
     */
    public int $tries = 1;

    /**
     * The number of seconds the job may run before timing out.
     */
    public int $timeout = 3600;

    /**
     * @param  string  $file  Path or filename of the backup to restore.
     * @param  string|null  $connectionName  Database connection to restore into (null = app default).
     */
    public function __construct(
        public string $file,
        public ?string $connectionName = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(DatabaseBackupService $service): void
    {
        $result = $service->restore($this->file, $this->connectionName);

        Log::info('Database restore job completed', $result + ['file' => $this->file]);
    }
}
