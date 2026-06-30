# Laravel Installer

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eii/laravel-installer.svg?style=flat-square)](https://packagist.org/packages/eii/laravel-installer)
[![Total Downloads](https://img.shields.io/packagist/dt/eii/laravel-installer.svg?style=flat-square)](https://packagist.org/packages/eii/laravel-installer)
[![PHP Version Requirement](https://img.shields.io/packagist/php-v/eii/laravel-installer.svg?style=flat-square)](https://packagist.org/packages/eii/laravel-installer)
[![Laravel Version](https://img.shields.io/badge/laravel-%3E%3D%2010.0-red?style=flat-square)](https://packagist.org/packages/eii/laravel-installer)
[![License](https://img.shields.io/packagist/l/eii/laravel-installer.svg?style=flat-square)](https://packagist.org/packages/eii/laravel-installer)

![Laravel Installer](https://eiitechsolutions.com/storage/packages/laravel-installer/banner-1.jpg?v=2)

This package features a multi-step wizard designed for a seamless Laravel application installation. It offers an intuitive interface that guides users through server requirement checks, environment configuration, database setup, and the creation of an initial admin user, all using Livewire components.

By including the Laravel installer in your application, you can eliminate the need to assist your clients with server setup. Clients can independently check server requirements, update the .env file, migrate the database, and link storage, all through a step-by-step installation guide.

## Screenshots

|                                                                                                                                     |                                                                                                                                     |
| :---------------------------------------------------------------------------------------------------------------------------------: | :---------------------------------------------------------------------------------------------------------------------------------: |
| <img width="1200" alt="laravel-installer step-1" src="https://eiitechsolutions.com/storage/packages/laravel-installer/step-1.webp?v=2"> | <img width="1200" alt="laravel-installer step-2" src="https://eiitechsolutions.com/storage/packages/laravel-installer/step-2.webp?v=2"> |
| <img width="1200" alt="laravel-installer step-3" src="https://eiitechsolutions.com/storage/packages/laravel-installer/step-3.webp?v=2"> | <img width="1200" alt="laravel-installer step-4" src="https://eiitechsolutions.com/storage/packages/laravel-installer/step-4.webp?v=2"> |
| <img width="1200" alt="laravel-installer step-5" src="https://eiitechsolutions.com/storage/packages/laravel-installer/step-5.webp?v=2"> | <img width="1200" alt="laravel-installer step-6" src="https://eiitechsolutions.com/storage/packages/laravel-installer/step-6.webp?v=2"> |



## ✨ What's New in v2.0.0

### 1️⃣ Livewire 4 Support
- Fully compatible with Livewire 4.x (now required).
- Updated component registration to use namespace-based resolution (`addNamespace`).
- Updated routing to use `Route::livewire()` syntax.
- Added `@livewireStyles` / `@livewireScripts` to installer layout.
- Fixed `#[Layout]` attribute syntax to use namespace-separated paths.

### 2️⃣ Bug Fixes
- Fixed `MailSettings::mount()` incorrectly reading environment data instead of mail data.
- Fixed undefined `$data` variable when mail step is optional.
- Fixed admin password being persisted to the progress file.

### 3️⃣ Optional Force Overwrite
- Added `--force` option to `installer:install` command for safer re-installation.
- Without `--force`: preserves your customizations (views, config, assets are not overwritten).
- With `--force`: prompts for confirmation before overwriting existing published files.

### ⚠️ Breaking Changes for v1.x Users
- **Livewire 3 is no longer supported.** Upgrade to Livewire 4 before updating this package.
- Published views/layouts from v1.x may need to be republished via `php artisan installer:install`.
## ✨ What’s New in v1.1.2

### 1️⃣ Loading State for Action Buttons

- Added loading indicators to step action buttons (Next / Finish).
- Improves user experience during time-consuming operations.
- Clearly informs users that processing is in progress and prevents duplicate submissions.

### 2️⃣ Improved Environment Input Handling

- Automatic trimming of input values to prevent validation errors caused by trailing spaces (common when copying & pasting).
- Quoted environment values when saving to .env, ensuring:
  - SMTP passwords containing spaces work correctly
  - No unexpected server errors (HTTP 500) during mail configuration

### 3️⃣ Spatie Permission Compatibility

- Added compatibility with spatie/laravel-permission
- Ensures smooth integration for applications using role & permission management
- Merged via PR #8 (thanks @vince844 🙌)

## Features

- **Easy Installation**: Easily Integrate in to your Laravel project.
- **Step-by-Step Wizard**: User-friendly interface for guided installation.
- **Server Requirement Checks**: Automatically verifies PHP version, extensions, and server configurations.
- **Environment Setup**: Configures `.env` file with database credentials, app name, and other essentials.
- **Database Migration and Seeding**: Runs migrations and seeds the database with initial data.
- **Link-Storage**: Runs storage link (configurable).
- **Admin User Creation**: Sets up a default administrator account securely.
- **Livewire Integration**: Dynamic, real-time updates, data validations without page reloads.
- **Customizable**: Easily extend or modify steps with additional livewire components to fit your application's needs.
- **Error Handling**: Graceful error messages and rollback options for failed installations.

## Requirements

- PHP >= 8.2
- Laravel >= 10.0
- Composer >= 2.0
- Livewire >= 4.0

## Installation

1. You can install the package via Composer:

```bash
composer require eii/laravel-installer
```

2. After installation, publish the package's assets and configuration by running the install command:

```bash
php artisan installer:install
```

3. Update the config/installer. (app_name, requirements, etc.)

## Updating

If you are upgrading from a previous version or need to republish assets, run the command with the `--force` flag:

```bash
php artisan installer:install --force
```

> 💡 Using `--force` will prompt you for confirmation before overwriting existing files. Without this flag, your customizations are preserved.


## Usage

- Navigate to `/install` in your browser to start the wizard.
- Follow the wizard steps to complete the installation.
- The wizard steps are defined in the `config/installer.php` file. You can add, remove, or reorder steps as needed.
- Modify the views published in `resources/views/vendor/installer` as needed.

## Configuration

The configuration file is located at `config/installer.php`. Key options include:

- `lock_file`: Path to the installation lock file to prevent re-running the installer.
- `redirect_after_install`: URL to redirect to after successful installation.
- `requirements`: Array of server requirements to check (e.g., PHP version, extensions like `pdo_mysql`).

## Troubleshooting

- **Resetting the Installer**: During development, you can reset the installation state by clearing sessions, progress, and lock files:
  ```bash
  php artisan installer:reset
  ```
- **Installation Fails on Requirements**: Ensure your server meets the listed requirements. Check the Laravel documentation for setup guides.
- **Database Connection Issues**: Verify your `.env` credentials and that the database server is running.
- **Livewire Not Working**: Make sure Livewire is properly installed and assets are published.
- **For more help, check the issues on GitHub or open a new one.**

## Development

If you want to modify the styling of the installer, you can rebuild the CSS using Tailwind CSS:

1. Install dependencies:

   ```bash
   npm install
   ```

2. Build the assets:

   ```bash
   npm run build
   ```

3. For real-time development:
   ```bash
   npm run watch
   ```

## Contributing

Contributions are welcome! Please follow these steps:

1. Contributions are welcome! Please follow these steps:
2. Create a new branch (`git checkout -b feature/YourFeature`).
3. Commit your changes (`git commit -m 'Add YourFeature'`).
4. Push to the branch (`git push origin feature/YourFeature`).
5. Open a Pull Request.

We appreciate bug reports, feature requests, and code improvements.

## License

This package is open-sourced software licensed under the MIT license.

## Credits

Amit Haldar (Eii Tech Solutions https://eiitechsolutions.com)  
Built with Laravel and Livewire

If you find this package useful, consider starring the repository on GitHub!
