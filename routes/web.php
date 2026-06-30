<?php

use Illuminate\Support\Facades\Route;
use Eii\Installer\Http\Controllers\InstallController;
// use Eii\Installer\Livewire\Install\InstallerWizard;

Route::middleware(['web', 'install'])->group(function () {
    Route::get('/install', [InstallController::class, 'start'])->name('install.start');
    // Livewire 4:
    Route::livewire('/install/{step}', 'installer::install.installer-wizard')->name('install.step');
});
