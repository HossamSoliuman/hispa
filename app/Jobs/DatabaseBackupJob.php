<?php

namespace App\Jobs;

use App\Service\DatabaseBackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DatabaseBackupJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * The number of seconds the job may run before timing out.
     */
    public int $timeout = 3600;

    /**
     * @param  string|null  $connectionName  Database connection to back up (null = app default).
     */
    public function __construct(
        public ?string $connectionName = null,
        public int $keepDays = 7,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(DatabaseBackupService $service): void
    {
        $result = $service->backup($this->connectionName, $this->keepDays);

        Log::info('Database backup job completed', $result);
    }
}
