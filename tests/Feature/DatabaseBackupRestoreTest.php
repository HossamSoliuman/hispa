<?php

namespace Tests\Feature;

use App\Jobs\DatabaseRestoreJob;
use App\Service\DatabaseBackupService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DatabaseBackupRestoreTest extends TestCase
{
    private const CONNECTION = 'restore_test';

    private string $databaseFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->databaseFile = storage_path('app/testing/restore_test_'.uniqid().'.sqlite');
        File::ensureDirectoryExists(dirname($this->databaseFile));
        touch($this->databaseFile);

        config([
            'database.connections.'.self::CONNECTION => [
                'driver' => 'sqlite',
                'database' => $this->databaseFile,
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);

        $this->seedFixtureDatabase();
    }

    protected function tearDown(): void
    {
        DB::connection(self::CONNECTION)->disconnect();
        DB::purge(self::CONNECTION);

        @unlink($this->databaseFile);

        foreach (glob(storage_path('app/backups').'/restore_test_*.sqlite.gz') ?: [] as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    private function seedFixtureDatabase(): void
    {
        $schema = DB::connection(self::CONNECTION)->getSchemaBuilder();

        $schema->create('widgets', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
            $table->text('notes')->nullable();
        });

        DB::connection(self::CONNECTION)->table('widgets')->insert([
            ['name' => 'alpha', 'notes' => "first; with a semicolon\nand a newline"],
            ['name' => 'beta', 'notes' => "it's a quote test"],
        ]);
    }

    private function widgetCount(): int
    {
        return DB::connection(self::CONNECTION)->table('widgets')->count();
    }

    public function test_service_round_trip_restores_data_after_it_is_wiped(): void
    {
        $service = app(DatabaseBackupService::class);

        $backup = $service->backup(self::CONNECTION, 0);

        $this->assertFileExists($backup['path']);
        $this->assertSame(2, $backup['rows']);
        $this->assertSame(1, $backup['tables']);

        DB::connection(self::CONNECTION)->table('widgets')->delete();
        $this->assertSame(0, $this->widgetCount());

        $result = $service->restore($backup['path'], self::CONNECTION);

        $this->assertSame(2, $result['rows']);
        $this->assertSame(2, $this->widgetCount());
        $this->assertSame(
            'alpha',
            DB::connection(self::CONNECTION)->table('widgets')->orderBy('id')->first()->name
        );
    }

    public function test_latest_backup_is_used_when_no_file_given(): void
    {
        $service = app(DatabaseBackupService::class);
        $backup = $service->backup(self::CONNECTION, 0);

        $this->assertSame($backup['path'], $service->latestBackup());
    }

    public function test_restore_job_restores_the_database(): void
    {
        $service = app(DatabaseBackupService::class);
        $backup = $service->backup(self::CONNECTION, 0);

        DB::connection(self::CONNECTION)->table('widgets')->delete();
        $this->assertSame(0, $this->widgetCount());

        DatabaseRestoreJob::dispatchSync($backup['path'], self::CONNECTION);

        $this->assertSame(2, $this->widgetCount());
    }

    public function test_backup_command_creates_a_file(): void
    {
        $this->artisan('db:backup', [
            '--connection' => self::CONNECTION,
            '--keep-days' => 0,
        ])->assertSuccessful();

        $this->assertNotNull(app(DatabaseBackupService::class)->latestBackup());
    }

    public function test_restore_command_with_force_restores_data(): void
    {
        $service = app(DatabaseBackupService::class);
        $backup = $service->backup(self::CONNECTION, 0);

        DB::connection(self::CONNECTION)->table('widgets')->delete();
        $this->assertSame(0, $this->widgetCount());

        $this->artisan('db:restore', [
            'file' => $backup['path'],
            '--connection' => self::CONNECTION,
            '--force' => true,
        ])->assertSuccessful();

        $this->assertSame(2, $this->widgetCount());
    }

    public function test_restore_fails_for_a_missing_backup_file(): void
    {
        $this->artisan('db:restore', [
            'file' => 'does-not-exist.sql.gz',
            '--connection' => self::CONNECTION,
            '--force' => true,
        ])->assertFailed();
    }
}
