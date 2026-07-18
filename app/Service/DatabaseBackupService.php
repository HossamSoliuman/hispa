<?php

namespace App\Service;

use App\Support\SqlStatementStreamer;
use Illuminate\Database\Connection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use SplFileInfo;
use Throwable;

/**
 * Creates and restores compressed, self-contained database backups.
 *
 * A MySQL/MariaDB backup is a gzipped SQL file containing the full schema
 * (`DROP TABLE` + `CREATE TABLE`) and data (`INSERT`) for every base table, so
 * a single restore rebuilds the database from empty with no migration step.
 * The dump is written inside one consistent-snapshot transaction and every
 * table's row count is verified, and again on restore, so a truncated or
 * partial backup can never be mistaken for a good one.
 *
 * SQLite databases are backed up and restored as a straight gzipped copy of
 * the database file.
 */
class DatabaseBackupService
{
    /**
     * Marker prefixing the JSON metadata comment line inside a SQL dump.
     */
    private const META_MARKER = 'HISPA-BACKUP-META';

    /**
     * Read/write buffer size for streaming (512 KB).
     */
    private const CHUNK_SIZE = 524_288;

    /**
     * Flush an INSERT batch once its value tuples pass this many bytes, keeping
     * each statement comfortably under the server's max_allowed_packet.
     */
    private const INSERT_FLUSH_BYTES = 500_000;

    /**
     * Absolute path to the directory backups are written to.
     */
    public function backupDirectory(): string
    {
        $directory = storage_path('app/backups');
        File::ensureDirectoryExists($directory);

        return $directory;
    }

    /**
     * Create a backup of the given connection and prune old ones.
     *
     * @return array{path:string,size:int,driver:string,tables:int,rows:int,pruned:int}
     */
    public function backup(?string $connection = null, int $keepDays = 7): array
    {
        $connection ??= Config::get('database.default');
        $driver = Config::get("database.connections.{$connection}.driver");

        if ($driver === null) {
            throw new RuntimeException("Database connection [{$connection}] is not configured.");
        }

        $directory = $this->backupDirectory();
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');

        $result = match ($driver) {
            'mysql', 'mariadb' => $this->backupMysql($connection, $directory, $timestamp),
            'sqlite' => $this->backupSqlite($connection, $directory, $timestamp),
            default => throw new RuntimeException("Unsupported database driver [{$driver}] for backup."),
        };

        $result['pruned'] = $keepDays > 0 ? $this->pruneOldBackups($keepDays) : 0;

        return $result;
    }

    /**
     * Restore the given backup file into the given connection.
     *
     * @return array{driver:string,database:string,tables:int,rows:int,statements:int}
     */
    public function restore(string $file, ?string $connection = null): array
    {
        $connection ??= Config::get('database.default');
        $driver = Config::get("database.connections.{$connection}.driver");

        if ($driver === null) {
            throw new RuntimeException("Database connection [{$connection}] is not configured.");
        }

        $path = $this->resolveBackupFile($file);

        if (str_ends_with($path, '.sqlite.gz')) {
            if ($driver !== 'sqlite') {
                throw new RuntimeException("Cannot restore a SQLite backup into a [{$driver}] database.");
            }

            return $this->restoreSqlite($connection, $path);
        }

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new RuntimeException("Cannot restore a SQL backup into a [{$driver}] database.");
        }

        return $this->restoreSql($connection, $path);
    }

    /**
     * List every backup file, newest first.
     *
     * @return list<SplFileInfo>
     */
    public function listBackups(): array
    {
        return collect(File::files($this->backupDirectory()))
            ->filter(fn (SplFileInfo $file): bool => str_ends_with($file->getFilename(), '.gz'))
            ->sortByDesc(fn (SplFileInfo $file): int => $file->getMTime())
            ->values()
            ->all();
    }

    /**
     * Absolute path of the most recent backup, or null when none exist.
     */
    public function latestBackup(): ?string
    {
        $backups = $this->listBackups();

        return $backups === [] ? null : $backups[0]->getPathname();
    }

    /**
     * Resolve a user-supplied file argument to an existing backup path.
     */
    public function resolveBackupFile(string $file): string
    {
        if (is_file($file)) {
            return $file;
        }

        $candidate = $this->backupDirectory().DIRECTORY_SEPARATOR.$file;

        if (is_file($candidate)) {
            return $candidate;
        }

        throw new RuntimeException("Backup file not found: {$file}");
    }

    /**
     * Delete backups older than the retention window and return how many were removed.
     */
    public function pruneOldBackups(int $keepDays): int
    {
        if ($keepDays <= 0) {
            return 0;
        }

        $cutoff = Carbon::now()->subDays($keepDays);
        $deleted = 0;

        foreach (File::files($this->backupDirectory()) as $file) {
            if (! str_ends_with($file->getFilename(), '.gz')) {
                continue;
            }

            if (Carbon::createFromTimestamp($file->getMTime())->lessThan($cutoff)) {
                File::delete($file->getPathname());
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Dump a MySQL/MariaDB database (schema + data) to a gzipped SQL file.
     *
     * @return array{path:string,size:int,driver:string,tables:int,rows:int}
     */
    protected function backupMysql(string $connection, string $directory, string $timestamp): array
    {
        $db = DB::connection($connection);
        $database = $db->getDatabaseName();
        $path = $directory.DIRECTORY_SEPARATOR."{$database}_{$timestamp}.sql.gz";
        $tables = $this->baseTables($db);

        $handle = gzopen($path, 'wb9');

        if ($handle === false) {
            throw new RuntimeException("Unable to open backup file for writing: {$path}");
        }

        $pdo = $db->getPdo();
        $bufferedByDefault = $pdo->getAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY);
        $totalRows = 0;

        try {
            $pdo->exec('START TRANSACTION WITH CONSISTENT SNAPSHOT');

            $expected = $this->rowCounts($pdo, $tables);

            $this->writeSqlHeader($handle, $database, $expected);

            $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

            $written = [];
            foreach ($tables as $table) {
                gzwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                gzwrite($handle, $this->createTableStatement($db, $table).";\n\n");
                $written[$table] = $this->dumpTableData($pdo, $handle, $table);
                $totalRows += $written[$table];
                gzwrite($handle, "\n");
            }

            $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $bufferedByDefault);

            gzwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");

            $pdo->exec('COMMIT');

            $this->assertBackupComplete($expected, $written);
        } catch (Throwable $e) {
            $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $bufferedByDefault);
            $this->rollBackQuietly($pdo);
            gzclose($handle);
            File::delete($path);
            throw $e;
        }

        gzclose($handle);

        return [
            'path' => $path,
            'size' => (int) File::size($path),
            'driver' => 'mysql',
            'tables' => count($tables),
            'rows' => $totalRows,
        ];
    }

    /**
     * Restore a gzipped SQL dump into a MySQL/MariaDB connection, verifying the
     * restored row counts against the metadata embedded in the file.
     *
     * @return array{driver:string,database:string,tables:int,rows:int,statements:int}
     */
    protected function restoreSql(string $connection, string $path): array
    {
        $meta = $this->readMeta($path);

        $db = DB::connection($connection);
        $pdo = $db->getPdo();
        $previousErrmode = $pdo->getAttribute(\PDO::ATTR_ERRMODE);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $handle = gzopen($path, 'rb');

        if ($handle === false) {
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, $previousErrmode);
            throw new RuntimeException("Unable to open backup file for reading: {$path}");
        }

        $streamer = new SqlStatementStreamer;
        $statements = 0;

        try {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

            while (! gzeof($handle)) {
                $chunk = gzread($handle, self::CHUNK_SIZE);

                if ($chunk === false) {
                    throw new RuntimeException("Failed reading backup file: {$path}");
                }

                foreach ($streamer->push($chunk) as $statement) {
                    $pdo->exec($statement);
                    $statements++;
                }
            }

            foreach ($streamer->flush() as $statement) {
                $pdo->exec($statement);
                $statements++;
            }

            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        } catch (Throwable $e) {
            gzclose($handle);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, $previousErrmode);
            throw $e;
        }

        gzclose($handle);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, $previousErrmode);

        $rows = $this->verifyRestore($db, $meta);

        return [
            'driver' => 'mysql',
            'database' => $db->getDatabaseName(),
            'tables' => count($this->listTables($db)),
            'rows' => $rows,
            'statements' => $statements,
        ];
    }

    /**
     * Compare the restored tables against the backup metadata and total the rows.
     *
     * @param  array{tables?:array<string,int>}|null  $meta
     */
    protected function verifyRestore(Connection $db, ?array $meta): int
    {
        $tables = $this->listTables($db);
        $pdo = $db->getPdo();
        $rows = 0;
        $mismatches = [];

        foreach ($tables as $table) {
            $count = (int) $pdo->query('SELECT COUNT(*) FROM `'.$table.'`')->fetchColumn();
            $rows += $count;

            if ($meta !== null && isset($meta['tables'][$table]) && $meta['tables'][$table] !== $count) {
                $mismatches[] = "{$table} (expected {$meta['tables'][$table]}, found {$count})";
            }
        }

        if ($meta !== null) {
            foreach (array_keys($meta['tables'] ?? []) as $expectedTable) {
                if (! in_array($expectedTable, $tables, true)) {
                    $mismatches[] = "{$expectedTable} (missing after restore)";
                }
            }
        }

        if ($mismatches !== []) {
            throw new RuntimeException('Restore verification failed: '.implode(', ', $mismatches));
        }

        return $rows;
    }

    /**
     * Write the metadata comment and session preamble of a SQL dump.
     *
     * @param  resource  $handle
     * @param  array<string,int>  $counts
     */
    protected function writeSqlHeader($handle, string $database, array $counts): void
    {
        $meta = [
            'format' => 'hispa-sql',
            'version' => 1,
            'driver' => 'mysql',
            'database' => $database,
            'generated_at' => Carbon::now()->toIso8601String(),
            'tables' => $counts,
        ];

        gzwrite($handle, '-- '.self::META_MARKER.' '.json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n");
        gzwrite($handle, "-- Self-contained backup of database {$database}\n");
        gzwrite($handle, "SET NAMES utf8mb4;\n");
        gzwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n");
        gzwrite($handle, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n");
    }

    /**
     * Read the JSON metadata comment from the top of a SQL dump, if present.
     *
     * @return array{tables?:array<string,int>}|null
     */
    protected function readMeta(string $path): ?array
    {
        $handle = gzopen($path, 'rb');

        if ($handle === false) {
            return null;
        }

        $marker = '-- '.self::META_MARKER.' ';
        $meta = null;
        $lines = 0;

        while (! gzeof($handle) && $lines < 50) {
            $line = gzgets($handle);
            $lines++;

            if ($line === false) {
                break;
            }

            if (str_starts_with($line, $marker)) {
                $decoded = json_decode(trim(substr($line, strlen($marker))), true);

                if (is_array($decoded)) {
                    $meta = $decoded;
                }

                break;
            }
        }

        gzclose($handle);

        return $meta;
    }

    /**
     * Return the exact `CREATE TABLE` statement for a table.
     */
    protected function createTableStatement(Connection $db, string $table): string
    {
        $row = (array) $db->select("SHOW CREATE TABLE `{$table}`")[0];

        return $row['Create Table'] ?? $row['Create View']
            ?? throw new RuntimeException("Unable to read schema for table [{$table}].");
    }

    /**
     * Count the rows of every table within the current snapshot.
     *
     * @param  list<string>  $tables
     * @return array<string,int>
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
     * Fail if any table wrote fewer rows than it holds.
     *
     * @param  array<string,int>  $expected
     * @param  array<string,int>  $written
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
            throw new RuntimeException('Backup is incomplete; row counts do not match for: '.implode(', ', $mismatches));
        }
    }

    /**
     * Roll back the snapshot transaction without masking the original error.
     */
    protected function rollBackQuietly(\PDO $pdo): void
    {
        try {
            $pdo->exec('ROLLBACK');
        } catch (Throwable) {
            // The connection is script-scoped and closes with the command.
        }
    }

    /**
     * List the base tables in a MySQL/MariaDB database, skipping views.
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
     * List the tables of any supported connection.
     *
     * @return list<string>
     */
    protected function listTables(Connection $db): array
    {
        if ($db->getDriverName() === 'sqlite') {
            return array_map(
                fn ($row): string => ((array) $row)['name'],
                $db->select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'")
            );
        }

        return $this->baseTables($db);
    }

    /**
     * Stream a single table's rows into batched multi-row INSERT statements and
     * return how many rows were written.
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

            if ($bytes >= self::INSERT_FLUSH_BYTES) {
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
     * Back up a SQLite database as a gzipped copy of its file.
     *
     * @return array{path:string,size:int,driver:string,tables:int,rows:int}
     */
    protected function backupSqlite(string $connection, string $directory, string $timestamp): array
    {
        $database = Config::get("database.connections.{$connection}.database");

        if (! is_string($database) || ! File::exists($database)) {
            throw new RuntimeException("SQLite database file not found: {$database}");
        }

        $db = DB::connection($connection);
        $tables = $this->listTables($db);
        $rows = 0;

        foreach ($tables as $table) {
            $rows += (int) $db->table($table)->count();
        }

        $name = pathinfo($database, PATHINFO_FILENAME);
        $path = $directory.DIRECTORY_SEPARATOR."{$name}_{$timestamp}.sqlite.gz";

        $source = fopen($database, 'rb');
        $handle = gzopen($path, 'wb9');

        if ($source === false || $handle === false) {
            throw new RuntimeException('Unable to open files for SQLite backup.');
        }

        while (! feof($source)) {
            gzwrite($handle, fread($source, self::CHUNK_SIZE));
        }

        fclose($source);
        gzclose($handle);

        return [
            'path' => $path,
            'size' => (int) File::size($path),
            'driver' => 'sqlite',
            'tables' => count($tables),
            'rows' => $rows,
        ];
    }

    /**
     * Restore a SQLite database by replacing its file with a decompressed copy.
     *
     * @return array{driver:string,database:string,tables:int,rows:int,statements:int}
     */
    protected function restoreSqlite(string $connection, string $path): array
    {
        $database = Config::get("database.connections.{$connection}.database");

        if (! is_string($database) || $database === '' || $database === ':memory:') {
            throw new RuntimeException('Cannot restore into an in-memory SQLite database.');
        }

        DB::connection($connection)->disconnect();

        $source = gzopen($path, 'rb');
        $target = fopen($database, 'wb');

        if ($source === false || $target === false) {
            throw new RuntimeException('Unable to open files for SQLite restore.');
        }

        while (! gzeof($source)) {
            fwrite($target, gzread($source, self::CHUNK_SIZE));
        }

        gzclose($source);
        fclose($target);

        $db = DB::connection($connection);
        $tables = $this->listTables($db);
        $rows = 0;

        foreach ($tables as $table) {
            $rows += (int) $db->table($table)->count();
        }

        return [
            'driver' => 'sqlite',
            'database' => $database,
            'tables' => count($tables),
            'rows' => $rows,
            'statements' => 0,
        ];
    }
}
