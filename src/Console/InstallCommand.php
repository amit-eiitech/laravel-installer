<?php

namespace Eii\Installer\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installer:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Laravel Installer package, publish assets, and prepare for usage.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Installing Laravel Installer...');

        $this->info('Publishing configuration...');
        $this->call('vendor:publish', ['--provider' => 'Eii\Installer\InstallerServiceProvider', '--tag' => 'config', '--force' => true]);

        $this->info('Publishing assets...');
        $this->call('vendor:publish', ['--provider' => 'Eii\Installer\InstallerServiceProvider', '--tag' => 'assets', '--force' => true]);

        $this->info('Publishing views...');
        $this->call('vendor:publish', ['--provider' => 'Eii\Installer\InstallerServiceProvider', '--tag' => 'views', '--force' => true]);

        $this->info('Laravel Installer installed successfully.');

        if ($this->confirm('Would you like to show some love by starring the repo?', true)) {
            if (PHP_OS_FAMILY === 'Darwin') {
                exec('open https://github.com/amit-eiitech/laravel-installer');
            } elseif (PHP_OS_FAMILY === 'Windows') {
                exec('start https://github.com/amit-eiitech/laravel-installer');
            } elseif (PHP_OS_FAMILY === 'Linux') {
                exec('xdg-open https://github.com/amit-eiitech/laravel-installer');
            }
            $this->line('Thank you!');
        }

        return self::SUCCESS;
    }
}
