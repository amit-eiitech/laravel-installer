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

        // Register Livewire 4 components
        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::addNamespace('installer', classNamespace: 'Eii\Installer\Livewire');
        }

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Eii\Installer\Console\InstallCommand::class,
                \Eii\Installer\Console\ResetInstallerCommand::class,
            ]);
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
