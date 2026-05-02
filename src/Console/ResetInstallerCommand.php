<?php

namespace Eii\Installer\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetInstallerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installer:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the installer by clearing sessions, progress, and lock files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting installer reset...');

        // 1. Clear session files
        $this->clearSessionFiles();

        // 2. Try to truncate sessions table if it exists
        $this->truncateSessionsTable();

        // 3. Delete progress file
        $progressFile = config('installer.options.progress_file');
        if (File::exists($progressFile)) {
            File::delete($progressFile);
            $this->info('✔ Installer progress file deleted.');
        }

        // 4. Delete lock file
        $lockFile = config('installer.options.lock_file');
        if (File::exists($lockFile)) {
            File::delete($lockFile);
            $this->info('✔ Installer lock file deleted.');
        }

        $this->info('Installer reset successfully. You can now re-run the wizard.');

        return 0;
    }

    /**
     * Clear session files from storage.
     */
    private function clearSessionFiles(): void
    {
        $sessionPath = storage_path('framework/sessions');
        if (File::isDirectory($sessionPath)) {
            $files = File::files($sessionPath);
            foreach ($files as $file) {
                if ($file->getFilename() !== '.gitignore') {
                    try {
                        File::delete($file);
                    } catch (\Exception $e) {
                        // Silence errors for locked files
                    }
                }
            }
            $this->info('✔ Session files cleared.');
        }
    }

    /**
     * Try to truncate the sessions table in the database.
     */
    private function truncateSessionsTable(): void
    {
        try {
            // We use a try-catch because the DB connection might not be configured or reachable
            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->truncate();
                $this->info('✔ Sessions table truncated.');
            }
        } catch (\Exception $e) {
            // Silence DB errors during reset
        }
    }
}
