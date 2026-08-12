<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * Native, zero-dependency backup service tuned for the realities of
 * CodeCanyon shared-hosting: dumps the database (mysqldump when the
 * binary is reachable, raw PDO fallback otherwise), bundles it with
 * the tenant storage folder into a timestamped zip, and writes the
 * result to storage/app/backups/.
 *
 * Restore is a zip-extract + single-file SQL import. Intentionally
 * simple so a support tech can recover a site in a single click —
 * more elaborate off-site pipelines (S3, encrypted archives) can
 * be bolted on later via spatie/laravel-backup if the script owner
 * wants them.
 */
class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (! is_dir($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Create a new backup zip containing the DB dump + uploaded files.
     * Returns the absolute path to the new archive.
     */
    public function create(array $options = []): string
    {
        $includeFiles = $options['files'] ?? true;
        $includeDb    = $options['database'] ?? true;

        $stamp   = now()->format('Ymd_His');
        $zipName = "leadhub_backup_{$stamp}.zip";
        $zipPath = $this->backupDir . DIRECTORY_SEPARATOR . $zipName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException(__('services/backup.unable_to_open_zip_for_writing', ['path' => $zipPath]));
        }

        // 1. Database
        if ($includeDb) {
            $sqlPath = $this->backupDir . DIRECTORY_SEPARATOR . "dump_{$stamp}.sql";
            $this->dumpDatabase($sqlPath);
            $zip->addFile($sqlPath, 'database.sql');
        }

        // 2. Uploaded files (storage/app/public)
        if ($includeFiles) {
            $publicStorage = storage_path('app/public');
            if (is_dir($publicStorage)) {
                $this->addDirectory($zip, $publicStorage, 'storage/public');
            }
        }

        // 3. Manifest
        $zip->addFromString('manifest.json', json_encode([
            'created_at'  => now()->toIso8601String(),
            'version'     => config('leadhub.version', '1.0.0'),
            'laravel'     => app()->version(),
            'php'         => PHP_VERSION,
            'includes_db' => $includeDb,
            'includes_files' => $includeFiles,
        ], JSON_PRETTY_PRINT));

        $zip->close();

        // Clean up the loose SQL file once it's safely inside the zip.
        if ($includeDb && isset($sqlPath) && file_exists($sqlPath)) {
            @unlink($sqlPath);
        }

        return $zipPath;
    }

    /**
     * Return an ordered list of existing backups:
     *  [ [name, path, size, created_at], ... ]
     */
    public function list(): array
    {
        $files = glob($this->backupDir . '/leadhub_backup_*.zip') ?: [];
        $rows  = [];

        foreach ($files as $file) {
            $rows[] = [
                'name'       => basename($file),
                'path'       => $file,
                'size'       => filesize($file),
                'created_at' => \Carbon\Carbon::createFromTimestamp(filemtime($file)),
            ];
        }

        usort($rows, fn ($a, $b) => $b['created_at'] <=> $a['created_at']);
        return $rows;
    }

    public function delete(string $name): bool
    {
        $path = $this->safePath($name);
        if (! $path || ! file_exists($path)) return false;
        return @unlink($path);
    }

    /**
     * Restore a backup archive: extracts `storage/public/*` back into
     * `storage/app/public/`, then imports `database.sql`. Returns a
     * summary array the UI can show.
     */
    public function restore(string $name): array
    {
        $path = $this->safePath($name);
        if (! $path || ! file_exists($path)) {
            throw new \RuntimeException(__('services/backup.backup_not_found'));
        }

        $workDir = $this->backupDir . DIRECTORY_SEPARATOR . 'restore_' . uniqid();
        File::makeDirectory($workDir, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException(__('services/backup.unable_to_open_archive'));
        }
        // Fix note: zip-slip guard. A maliciously-crafted backup archive
        // with entries like `../../public/shell.php` would write outside
        // $workDir without this check (RCE-equivalent on the host). The
        // sibling extract paths in UpdaterService and ModuleManagerService
        // already validate; this path was the missing third.
        \App\Support\ZipSecurity::validateEntries($zip);
        $zip->extractTo($workDir);
        $zip->close();

        $filesRestored = 0;
        $publicBackup = $workDir . '/storage/public';
        if (is_dir($publicBackup)) {
            $target = storage_path('app/public');
            File::ensureDirectoryExists($target);
            $filesRestored = $this->copyDirectory($publicBackup, $target);
        }

        $rowsImported = 0;
        $sqlFile = $workDir . '/database.sql';
        if (file_exists($sqlFile)) {
            $rowsImported = $this->importSql($sqlFile);
        }

        File::deleteDirectory($workDir);

        return [
            'files_restored' => $filesRestored,
            'rows_imported'  => $rowsImported,
            'backup'         => $name,
        ];
    }

    public function path(string $name): ?string
    {
        return $this->safePath($name);
    }

    /**
     * Smoke-test a backup archive without restoring it.
     *
     * Validates that the zip is well-formed, contains a `database.sql`
     * entry with non-zero bytes, and roughly counts SQL statements by
     * splitting on `";\n"` (the same delimiter mysqldump emits and
     * `importSql()` consumes during a real restore).
     *
     * Returned shape:
     *   [ 'ok' => bool, 'sql_statements' => int, 'errors' => string[] ]
     *
     * Designed to be cheap enough to run on every backup creation (so
     * the SA UI can flag corrupt archives early) and on a nightly
     * cron against the latest archive (so silent corruption surfaces
     * before someone needs to restore).
     */
    public function verify(string $path): array
    {
        $errors = [];

        if (! file_exists($path)) {
            return ['ok' => false, 'sql_statements' => 0, 'errors' => [__('services/backup.backup_file_not_found', ['path' => $path])]];
        }

        $zip = new ZipArchive();
        $opened = $zip->open($path, ZipArchive::CHECKCONS);
        if ($opened !== true) {
            return [
                'ok'             => false,
                'sql_statements' => 0,
                'errors'         => [__('services/backup.zip_open_error_code', ['code' => $opened])],
            ];
        }

        $sqlIndex = $zip->locateName('database.sql');
        if ($sqlIndex === false) {
            $errors[] = __('services/backup.missing_database_sql');
            $zip->close();
            return ['ok' => false, 'sql_statements' => 0, 'errors' => $errors];
        }

        $stat = $zip->statIndex($sqlIndex) ?: [];
        $size = (int) ($stat['size'] ?? 0);
        if ($size <= 0) {
            $errors[] = __('services/backup.database_sql_empty');
        }

        // H19: stream the SQL through ZipArchive::getStream — getFromIndex
        // would load the entire 300-800 MB dump into PHP memory and OOM
        // on the very installs that need verification most (CodeCanyon
        // buyers running shared-hosting with memory_limit=128M).  We
        // only need a count of statement terminators, not the bytes
        // themselves, so 1 MiB chunks are plenty.
        $sqlStatements = 0;
        $name          = $zip->getNameIndex($sqlIndex) ?: 'database.sql';
        $stream        = $zip->getStream($name);
        if ($stream === false) {
            $errors[] = __('services/backup.cannot_open_sql_stream');
        } else {
            while (! feof($stream)) {
                $buf = fread($stream, 1024 * 1024);
                if ($buf === false) {
                    break;
                }
                $sqlStatements += substr_count($buf, ";\n");
            }
            fclose($stream);
        }

        if ($sqlStatements === 0 && $size > 0 && $errors === []) {
            $errors[] = __('services/backup.no_recognizable_sql_statements');
        }

        $zip->close();

        return [
            'ok'             => $errors === [],
            'sql_statements' => $sqlStatements,
            'errors'         => $errors,
        ];
    }

    /* --------------------------------------------------------------- */

    protected function safePath(string $name): ?string
    {
        // Prevent ../ traversal: only basenames allowed.
        $base = basename($name);
        if (! preg_match('/^leadhub_backup_\d{8}_\d{6}\.zip$/', $base)) {
            return null;
        }
        return $this->backupDir . DIRECTORY_SEPARATOR . $base;
    }

    protected function dumpDatabase(string $destination): void
    {
        $connection = config('database.default');
        $config     = config("database.connections.{$connection}");

        if (($config['driver'] ?? null) !== 'mysql') {
            throw new \RuntimeException(__('services/backup.only_mysql_supported'));
        }

        // Prefer mysqldump when the binary is reachable AND exec() is available
        // — it's 20× faster. On locked-down shared hosting exec/shell_exec are
        // listed in php.ini disable_functions; PHP removes them entirely, so an
        // unqualified call dies with "Call to undefined function
        // App\Services\exec()". Guard with function_exists() and fall straight
        // through to the pure-PHP PDO dump instead of fataling the whole update.
        $binary = function_exists('exec') ? $this->findMysqldump() : null;
        if ($binary) {
            $cmd = sprintf(
                '%s --no-tablespaces --single-transaction --quick --lock-tables=false -h%s -P%s -u%s %s %s > %s 2>&1',
                escapeshellarg($binary),
                escapeshellarg($config['host']),
                escapeshellarg((string) ($config['port'] ?? 3306)),
                escapeshellarg($config['username']),
                empty($config['password']) ? '' : '-p' . escapeshellarg($config['password']),
                escapeshellarg($config['database']),
                escapeshellarg($destination),
            );

            \exec($cmd, $out, $code);
            if ($code === 0 && filesize($destination) > 0) {
                return;
            }
            // fall through to PDO fallback on failure
        }

        $this->dumpDatabasePdo($destination);
    }

    protected function findMysqldump(): ?string
    {
        foreach (['/usr/bin/mysqldump', '/usr/local/bin/mysqldump', 'mysqldump'] as $candidate) {
            if (@is_executable($candidate)) return $candidate;
        }

        if (! function_exists('shell_exec')) {
            return null;
        }
        $which = @\shell_exec('which mysqldump 2>/dev/null');
        $which = $which ? trim($which) : '';
        return $which && is_executable($which) ? $which : null;
    }

    /**
     * Pure-PHP PDO fallback dump. Slower but works on restrictive hosts
     * where shell_exec is disabled.
     */
    protected function dumpDatabasePdo(string $destination): void
    {
        $pdo = DB::connection()->getPdo();
        $handle = fopen($destination, 'w');
        if (! $handle) {
            throw new \RuntimeException(__('services/backup.unable_to_write_dump', ['path' => $destination]));
        }

        fwrite($handle, "-- LeadHub native backup — " . now() . "\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        // Bound the dump to the connection's database explicitly.
        // `SHOW TABLES` already restricts to the current schema in
        // practice, but on hosting setups where the DB user has been
        // granted access to extra databases (some shared cPanel
        // accounts share a single user across multiple workspaces) it
        // could pull tables from outside the application schema.
        // Querying information_schema with TABLE_SCHEMA = DATABASE()
        // makes the intent explicit and additionally filters out
        // VIEWs / SYSTEM VIEWs that SHOW TABLES would otherwise list.
        $tables = $pdo->query(
            "SELECT TABLE_NAME FROM information_schema.tables "
            . "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE'"
        )->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $create = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_NUM);
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($handle, $create[1] . ";\n\n");

            $rows = $pdo->query("SELECT * FROM `{$table}`");
            while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
                $cols = array_map(fn ($c) => "`{$c}`", array_keys($row));
                $vals = array_map(
                    fn ($v) => $v === null ? 'NULL' : $pdo->quote((string) $v),
                    array_values($row)
                );
                fwrite($handle, "INSERT INTO `{$table}` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ");\n");
            }
            fwrite($handle, "\n");
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }

    protected function importSql(string $sqlFile): int
    {
        // H19: streaming statement reader — accumulate chars into a
        // sliding buffer until the ";\n" mysqldump terminator, exec,
        // drop the consumed prefix, repeat.  Bounds memory regardless
        // of dump size; the previous file_get_contents approach OOM'd
        // on 500 MB+ dumps.
        $pdo    = DB::connection()->getPdo();
        $fp     = @fopen($sqlFile, 'r');
        if ($fp === false) {
            \Log::warning('Backup restore: could not open ' . $sqlFile);
            return 0;
        }

        $buffer = '';
        $count  = 0;

        $exec = function (string $stmt) use ($pdo, &$count) {
            $stmt = trim($stmt);
            if ($stmt === '' || str_starts_with($stmt, '--')) {
                return;
            }
            try {
                $pdo->exec($stmt);
                $count++;
            } catch (\Throwable $e) {
                \Log::warning('Backup restore SQL failed: ' . $e->getMessage());
            }
        };

        while (! feof($fp)) {
            $chunk = fread($fp, 1024 * 1024); // 1 MiB chunks
            if ($chunk === false) {
                break;
            }
            $buffer .= $chunk;

            // Drain every complete statement out of the buffer.
            while (($pos = strpos($buffer, ";\n")) !== false) {
                $exec(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 2);
            }
        }
        fclose($fp);

        // Tail statement without a trailing ";\n" — mysqldump usually
        // ends with one, but be defensive.
        if (trim($buffer) !== '') {
            $exec($buffer);
        }

        return $count;
    }

    protected function addDirectory(ZipArchive $zip, string $source, string $zipPrefix): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
        );

        foreach ($iterator as $item) {
            $relative = substr($item->getPathname(), strlen($source) + 1);
            $entry    = $zipPrefix . '/' . str_replace('\\', '/', $relative);
            if ($item->isDir()) {
                $zip->addEmptyDir($entry);
            } else {
                $zip->addFile($item->getPathname(), $entry);
            }
        }
    }

    protected function copyDirectory(string $source, string $destination): int
    {
        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
        );

        foreach ($iterator as $item) {
            $relative = substr($item->getPathname(), strlen($source) + 1);
            $target   = $destination . DIRECTORY_SEPARATOR . $relative;

            if ($item->isDir()) {
                File::ensureDirectoryExists($target);
            } else {
                File::ensureDirectoryExists(dirname($target));
                copy($item->getPathname(), $target);
                $count++;
            }
        }

        return $count;
    }
}
