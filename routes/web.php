<?php

use Illuminate\Support\Facades\Route;
use Eii\Installer\Http\Controllers\InstallController;
use Eii\Installer\Livewire\Install\InstallerWizard;

Route::middleware(['web', 'install'])->group(function () {
    Route::get('/install', [InstallController::class, 'start'])->name('install.start');
    Route::get('/install/{step}', InstallerWizard::class)->name('install.step');
});
