<?php

namespace Eii\Installer;

use Illuminate\Support\ServiceProvider;

class InstallerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Assets (CSS/JS/images etc.)
        $this->publishes([
            __DIR__ . '/resources/public' => public_path('vendor/installer'),
        ], 'assets');

        // Config
        $this->publishes([
            __DIR__ . '/config/installer.php' => config_path('installer.php'),
        ], 'config');

        // Views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'installer');

        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/installer'),
        ], 'views');

        // Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Register Livewire components
        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::component('installer::install.installer-wizard', \Eii\Installer\Livewire\Install\InstallerWizard::class);
            \Livewire\Livewire::component('installer::install.welcome', \Eii\Installer\Livewire\Install\Welcome::class);
            \Livewire\Livewire::component('installer::install.server-requirements', \Eii\Installer\Livewire\Install\ServerRequirements::class);
            \Livewire\Livewire::component('installer::install.environment-settings', \Eii\Installer\Livewire\Install\EnvironmentSettings::class);
            \Livewire\Livewire::component('installer::install.mail-settings', \Eii\Installer\Livewire\Install\MailSettings::class);
            \Livewire\Livewire::component('installer::install.create-admin', \Eii\Installer\Livewire\Install\CreateAdmin::class);
            \Livewire\Livewire::component('installer::install.finish', \Eii\Installer\Livewire\Install\Finish::class);
        }
    }

    public function register(): void
    {
        // Merge default config
        $this->mergeConfigFrom(
            __DIR__ . '/config/installer.php',
            'installer'
        );

        $router = $this->app['router'];
        $router->aliasMiddleware('install', \Eii\Installer\Http\Middleware\CheckInstallation::class);
    }
}
