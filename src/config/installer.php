<?php

return [

    'app_name' => 'Eii Laravel Installer',

    /*
    |--------------------------------------------------------------------------
    | Installation Steps
    |--------------------------------------------------------------------------
    | Define the steps of the installer wizard.
    | Each step has:
    | - key: unique identifier
    | - label: human-readable name
    | - component/controller: which Livewire component or controller handles it
    | - optional: whether this step can be skipped
    */

    'steps' => [
        [
            'key' => 'welcome',
            'label' =>  'Welcome',
            'description' => 'Getting started',
            'component' => \Eii\Installer\Livewire\Install\Welcome::class,
        ],
        [
            'key' => 'requirements',
            'label' => 'Server Requirements',
            'description' => 'Check all necessary requirements',
            'component' => \Eii\Installer\Livewire\Install\ServerRequirements::class,
        ],
        [
            'key' => 'environment',
            'label' => 'Environment Settings',
            'description' => 'Gather environmental settings',
            'component' => \Eii\Installer\Livewire\Install\EnvironmentSettings::class,
        ],
        [
            'key' => 'admin',
            'label' => 'Create Admin',
            'description' => 'Create Admin User',
            'component' => \Eii\Installer\Livewire\Install\CreateAdmin::class,
        ],
        [
            'key' => 'finish',
            'label' => 'Finish',
            'description' => 'Finish setup',
            'component' => \Eii\Installer\Livewire\Install\Finish::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Server Requirements
    |--------------------------------------------------------------------------
    */
    'requirements' => [
        'php' => '8.1.0',
        'extensions' => [
            'openssl',
            'pdo',
            'mbstring',
            'tokenizer',
            'xml',
            'ctype',
            'json',
        ],
        'permissions' => [
            'storage/' => 'writable',
            'bootstrap/cache/' => 'writable',
            'storage/'          => 'writable',
            'storage/app/'      => 'writable',
            'storage/framework/' => 'writable',
            'storage/logs/'     => 'writable',
            'bootstrap/cache/'  => 'writable',
            '.env'              => 'writable',
        ],
        'environment' => [
            'production' => true,       // True for production, False for Local
            'debug' => false,           // Set debug
            'database' => true,         // Ask for mail details
            'mail' => false,
        ],
        'create_admin' => true,     // True to use the 'create admin' step 
        'link_storage' => true,     // True to link storage
        'seed_database' => true,    // Enable DB seeding after migrations
    ],

    /*
    |--------------------------------------------------------------------------
    | Installer Options
    |--------------------------------------------------------------------------
    */
    'options' => [
        'lock_file' => storage_path('installed.lock'),
        'progress_file' => storage_path('install-progress.json'),
        'redirect_after_install' => '/',
    ],

];
