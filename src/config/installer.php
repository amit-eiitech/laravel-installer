<?php

return [

    'app_name' => 'Eii Laravel Installer',
    'run_installer' => true,

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
            'description' => 'Initialize',
            'component' => \Eii\Installer\Livewire\Install\Welcome::class,
        ],
        [
            'key' => 'requirements',
            'label' => 'Server requirements',
            'description' => 'Make sure all necessary requirements are met',
            'component' => \Eii\Installer\Livewire\Install\ServerRequirements::class,
        ],
        [
            'key' => 'environment',
            'label' => 'Environmental settings',
            'description' => 'Collect environmental settings',
            'component' => \Eii\Installer\Livewire\Install\EnvironmentSettings::class,
        ],
        [
            'key' => 'mail',
            'label' => 'Mail Settings',
            'description' => 'Outgoing mail settings',
            'component' => \Eii\Installer\Livewire\Install\MailSettings::class,
            'optional' => true,
        ],
        [
            'key' => 'admin',
            'label' => 'Create administrator',
            'description' => 'Create admin user',
            'component' => \Eii\Installer\Livewire\Install\CreateAdmin::class,
            'optional' => true,
        ],
        [
            'key' => 'finish',
            'label' => 'Finish',
            'description' => 'Complete the setup',
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
            'database' => true,         // Ask for database details. Set to false if there is no database. 
            'mail' => true,
        ],
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
