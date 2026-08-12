<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

/**
 * Creates a full backup from CLI / cron. Pair with a nightly cron
 * entry to keep a rolling set of archives:
 *
 *   0 3 * * *  cd /var/www/leadhub && php artisan leadhub:backup --prune=7
 */
class RunBackup extends Command
{
    protected $signature = 'leadhub:backup {--prune= : Delete backups older than N days after creating the new one}';
    protected $description = 'Create a database + files backup and optionally prune old archives.';

    public function handle(BackupService $svc): int
    {
        $this->info('Creating backup…');

        try {
            $path = $svc->create(['database' => true, 'files' => true]);
            $this->info('Backup created: ' . basename($path) . ' (' . $this->human(filesize($path)) . ')');
        } catch (\Throwable $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        if ($pruneDays = (int) $this->option('prune')) {
            $cutoff = now()->subDays($pruneDays);
            $deleted = 0;
            foreach ($svc->list() as $b) {
                if ($b['created_at']->lt($cutoff)) {
                    $svc->delete($b['name']);
                    $deleted++;
                }
            }
            $this->info("Pruned {$deleted} backups older than {$pruneDays} days.");
        }

        return self::SUCCESS;
    }

    protected function human(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return number_format($bytes, $i === 0 ? 0 : 2) . ' ' . $units[$i];
    }
}
