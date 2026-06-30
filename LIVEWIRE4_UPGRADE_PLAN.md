# Livewire 4 + Laravel 11/12/13 Compatibility Plan

## Recommended Composer Constraints

For your package, use **Illuminate component constraints** (not `laravel/framework`) and a PHP baseline that matches Livewire 4.

```json
{
  "require": {
    "php": "^8.2",
    "livewire/livewire": "^4.0",
    "illuminate/support": "^11.0|^12.0|^13.0",
    "illuminate/routing": "^11.0|^12.0|^13.0",
    "illuminate/view": "^11.0|^12.0|^13.0",
    "illuminate/console": "^11.0|^12.0|^13.0",
    "illuminate/filesystem": "^11.0|^12.0|^13.0",
    "illuminate/database": "^11.0|^12.0|^13.0"
  }
}
```

If you want to target modern production stacks only, set PHP to `^8.3` instead.

---

## Why these constraints

- **Livewire 4 minimum** is Laravel 11 + PHP 8.2.
- Using `illuminate/*` keeps package dependency resolution cleaner than pinning `laravel/framework`.
- Supporting `^11|^12|^13` gives broad adoption while staying inside Livewire 4 compatibility.

---

## Upgrade Steps (Practical)

### 1) Dependency update
- Update `composer.json` constraints as above.
- Run:
  - `composer update`
  - `composer why-not livewire/livewire ^4.0` (if dependency conflicts appear)

### 2) Livewire event payload modernization
Your package currently dispatches many events with array payloads. Prefer named params for Livewire 4 style consistency.

Example:
- from: `$this->dispatch('wizard.error', ['message' => '...']);`
- to: `$this->dispatch('wizard.error', message: '...');`

And adjust listeners accordingly.

### 3) Ensure layout includes Livewire assets
In `src/resources/views/layouts/installer.blade.php`, include:
- `@livewireStyles` in `<head>`
- `@livewireScripts` before `</body>`

### 4) Normalize package view layout names
Use `installer::layouts.installer` consistently in all Livewire components.

### 5) Dynamic component rendering cleanup
In wizard view, avoid passing parent component instances into child components. Prefer dynamic component rendering via class name cleanly.

### 6) Minor bugs to fix while upgrading
- `MailSettings::mount()` should load prior `mail` step data, not `environment` data.
- Ensure `$data` is always defined in optional mail flow before dispatching `wizard.stepCompleted`.
- README path should reference `config/installer.php` (not `config/laravel-installer.php`).
- Normalize `#[Layout(...)]` usage to `installer::layouts.installer` in package components.
- Add `@livewireStyles` / `@livewireScripts` to installer layout if missing.

### 7) Add compatibility test matrix (strongly recommended)

Test these combinations:

1. PHP 8.2 + Laravel 11 + Livewire 4
2. PHP 8.3 + Laravel 12 + Livewire 4
3. PHP 8.3 + Laravel 13 + Livewire 4

Also validate full installer behavior end-to-end:
- `/install` entry and redirect/resume behavior
- step progression and optional skip logic
- environment write behavior (`.env` updates)
- migration + seeding run path and failure handling
- lock/progress file create/delete behavior
- final redirect behavior

Use package tests (preferably with Orchestra Testbench) and run the matrix in CI before release.

---

## Compatibility Test Matrix (at-a-glance)

| PHP | Laravel | Livewire | Status |
|---|---|---|---|
| 8.2 | 11.x | 4.x | Required |
| 8.3 | 12.x | 4.x | Required |
| 8.3 | 13.x | 4.x | Required |
| 8.4 (optional) | 13.x | 4.x | Nice-to-have |

Keep this table updated as new Laravel minor versions are released.

---

## Suggested CI checks

- Dependency install and autoload dump
- Static analysis/linting (if configured)
- Package test suite
- Optional smoke test of demo installer route

A minimal CI flow should fail fast on dependency conflicts and run tests on each matrix job.

---

## Suggested shell commands for local verification

```powershell
composer validate
composer update
composer why-not livewire/livewire ^4.0
```

```powershell
php artisan test
```

```powershell
php artisan installer:reset
```

```powershell
php artisan route:list | findstr install
``` 
---

## Release Strategy

- Create a new major/minor package release indicating Livewire 4 support.
- Update README requirements and upgrade notes.
- Tag release after CI matrix passes.


Maintainer note: if you decide to keep Livewire 3 support in the same branch, use broader constraints and conditional handling, but maintenance complexity increases significantly.
