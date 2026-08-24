<?php

namespace Eii\Installer;

use Illuminate\Support\ServiceProvider;

class InstallerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureInstallerStorage();

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

    protected function configureInstallerStorage(): void
    {
        if ($this->app->runningInConsole() || ! $this->isInstallerRequest()) {
            return;
        }

        // Fresh Laravel applications may use database-backed sessions/cache by default.
        // During installation the required database tables may not exist yet, so keep
        // installer requests independent from the application's database state.
        config([
            'session.driver' => 'file',
            'session.connection' => null,
            'cache.default' => 'file',
        ]);
    }

    protected function isInstallerRequest(): bool
    {
        $request = request();

        if ($request->is('install', 'install/*')) {
            return true;
        }

        return $this->isInstallerReferer($request->headers->get('referer'));
    }

    protected function isInstallerReferer(?string $referer): bool
    {
        if (empty($referer)) {
            return false;
        }

        $path = trim((string) parse_url($referer, PHP_URL_PATH), '/');

        return $path === 'install' || str_starts_with($path, 'install/');
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
