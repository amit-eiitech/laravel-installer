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
    protected $signature = 'installer:install {--force : Overwrite existing published files}';

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

        $force = $this->option('force');

        $publishOptions = ['--provider' => 'Eii\Installer\InstallerServiceProvider'];

        if ($force && $this->confirm('Overwrite existing published files?', true)) {
            $publishOptions['--force'] = true;
        }

        $this->info('Publishing configuration...');
        $this->call('vendor:publish', array_merge($publishOptions, ['--tag' => 'config']));

        $this->info('Publishing assets...');
        $this->call('vendor:publish', array_merge($publishOptions, ['--tag' => 'assets']));

        $this->info('Publishing views...');
        $this->call('vendor:publish', array_merge($publishOptions, ['--tag' => 'views']));

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
