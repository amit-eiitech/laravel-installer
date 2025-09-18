# Laravel Installer

A package to install Laravel applications with wizard steps.

## Features

- Multi-step installation wizard using Livewire components
- Publishes configuration, views, and assets for easy customization
- Checks server requirements and environment settings
- Creates default admin user during installation
- Fully extendable and customizable

## Installation

1. Require the package via Composer:

```bash
composer require eii/laravel-installer
```

2. Publish package files:

```bash
php artisan vendor:publish --provider="Eii\Installer\InstallerServiceProvider" --tag=installer-config
php artisan vendor:publish --provider="Eii\Installer\InstallerServiceProvider" --tag=views
php artisan vendor:publish --provider="Eii\Installer\InstallerServiceProvider" --tag=installer-assets
```

3. Apply the `install` middleware to your routes to protect them until installation is complete.

## Usage
- Access the installer routes in your browser (e.g., `/install`)
- Follow the wizard steps to complete the installation
- Modify published views and config as needed

## License

This package is open-sourced software licensed under the MIT license.

## Author

Amit Haldar