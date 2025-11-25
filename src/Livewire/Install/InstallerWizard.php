<?php

namespace Eii\Installer\Livewire\Install;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class InstallerWizard extends Component
{
    public string $stepKey;
    public Collection $steps;
    public int $currentIndex = 0;
    public array $progress = [];
    public bool $canProceed = true;
    public bool $skippable = false;
    public bool $showWaitScreen = false;

    /**
     * Initialize component with the current step and validate progress.
     *
     * @param string $step The key of the current installation step.
     * @return void|\Illuminate\Http\RedirectResponse
     */
    public function mount($step)
    {
        $this->steps = collect(config('installer.steps'));
        $this->stepKey = $step;
        $this->currentIndex = $this->steps->search(fn($s) => $s['key'] === $this->stepKey);
        $this->skippable = $this->steps[$this->currentIndex]['optional'] ?? false;

        if ($this->currentIndex === false) {
            abort(404, "Invalid installer step.");
        }

        $this->loadProgress();

        if (isset($this->progress['raw_env_data'])) {
            // If progress file has error 
            if (isset($this->progress['error'])) {
                // show the error
                session()->flash('installer.error', $this->progress['error']);

                // Remove the error from the file
                unset($this->progress['error']);
                $this->saveProgress();
            } else {
                // If no error and has raw data,
                // Move the raw data to completed data
                $this->progress['data']['environment'] = $this->progress['raw_env_data'];

                // remove raw data
                unset($this->progress['raw_env_data']);

                // Update the current step
                $this->progress['current_step'] = "environment";

                // Update the progress file
                $this->saveProgress();

                // Move to the next step
                $savedIndex = $this->steps->search(fn($s) => $s['key'] === 'environment');
                return $this->redirect(route('install.step', $this->steps[$savedIndex + 1]['key']));
            }
        }

        $savedStep = $this->progress['current_step'] ?? null;
        $savedIndex = $this->steps->search(fn($s) => $s['key'] === $savedStep);

        if ($savedIndex !== false && $this->currentIndex > $savedIndex + 1) {
            return $this->redirect(route('install.step', $this->steps[$savedIndex + 1]['key']));
        }
    }

    /**
     * Handle errors dispatched from child components.
     *
     * @param array $payload Error message payload.
     * @return void
     */
    #[On('wizard.error')]
    public function handleError(array $payload): void
    {
        session()->flash('installer.error', $payload['message']);
        $this->canProceed = false;
    }

    /**
     * Enable the next step button.
     *
     * @return void
     */
    #[On('wizard.canProceed')]
    public function enableNext(): void
    {
        $this->canProceed = true;
    }

    /**
     * Disable the next step button.
     *
     * @return void
     */
    #[On('wizard.cannotProceed')]
    public function disableNext(): void
    {
        $this->canProceed = false;
    }

    /**
     * Trigger step completion in the child component.
     *
     * @return void
     */
    public function completeStep(): void
    {
        $this->dispatch('completeStep', $this->stepKey);
    }

    /**
     * Trigger step completion without any data.
     *
     * @return void
     */
    public function skipStep(): void
    {
        $this->saveStep();
    }

    /**
     * Render the installer wizard view with step data.
     *
     * @return \Illuminate\View\View
     */
    #[Layout('installer::layouts.installer')]
    public function render()
    {
        return view('installer::livewire.install.installer-wizard', [
            'step' => $this->steps[$this->currentIndex],
            'steps' => $this->steps,
            'currentIndex' => $this->currentIndex,
        ]);
    }


    /**
     * Save step data and redirect to the next step or finish page.
     *
     * @param array $payload Data from the completed step.
     * @return \Illuminate\Http\RedirectResponse
     */
    #[On('wizard.stepCompleted')]
    public function saveStep($payload = [])
    {
        $this->showWaitScreen = true;

        // Get the progress data
        $progressFile = config('installer.options.progress_file');
        $progress = File::exists($progressFile)
            ? json_decode(File::get($progressFile), true)
            : ['data' => []];

        try {

            // In case of environment setup, 
            if ($this->stepKey === "environment") {
                $this->runEnvironmentSetup($payload['data']);
            }

            // In case of environment setup, 
            if ($this->stepKey === "mail") {
                $this->runMailSetup($payload['data'] ?? []);
            }

            // Save the progress data
            if (!empty($payload)) {
                $progress['data'][$this->stepKey] = $payload;
            }

            // Save the progress file
            $progress['current_step'] = $this->stepKey;
            File::put($progressFile, json_encode($progress, JSON_PRETTY_PRINT));

            // Move to the next step
            $nextStepKey = $this->getNextStepKey();
            return $this->redirect(route('install.step', $nextStepKey ?? config('installer.redirect_after_install', '/')));
        } catch (\Throwable $th) {
            $progress['error'] = $th->getMessage();
            File::put($progressFile, json_encode($progress, JSON_PRETTY_PRINT));
            $this->showWaitScreen = false;

            session()->flash('installer.error', $th->getMessage());
        }
    }

    /**
     * Get the key of the next installation step.
     *
     * @return string|null
     */
    private function getNextStepKey(): ?string
    {
        return $this->steps[$this->currentIndex + 1]['key'] ?? null;
    }

    /**
     * Load installation progress from file or initialize it.
     *
     * @return void
     */
    private function loadProgress(): void
    {
        $progressFile = config('installer.options.progress_file');
        $this->progress = File::exists($progressFile)
            ? json_decode(File::get($progressFile), true)
            : [
                'current_step' => $this->steps[0]['key'],
                'data' => [],
            ];
        $this->saveProgress();
    }

    /**
     * Save current installation progress to file.
     *
     * @return void
     */
    private function saveProgress(): void
    {
        File::put(config('installer.options.progress_file'), json_encode($this->progress, JSON_PRETTY_PRINT));
    }

    /**
     * Configure environment settings, database, and storage link.
     *
     * @param array $data Environment configuration data.
     * @throws \Exception If database connection, migration, or storage link fails.
     * @return void
     */
    private function runEnvironmentSetup(array $data): void
    {
        if (config('installer.requirements.environment.database')) {
            if (empty($data['db_database'])) {
                throw new \Exception("DB_DATABASE is missing. Please provide a database name.");
            }

            // 1️⃣ Reload config with new credentials
            Artisan::call('config:clear');
            config([
                'database.connections.mysql.host' => $data['db_host'],
                'database.connections.mysql.port' => $data['db_port'] ?? 3306,
                'database.connections.mysql.database' => $data['db_database'],
                'database.connections.mysql.username' => $data['db_username'],
                'database.connections.mysql.password' => $data['db_password'],
            ]);

            DB::purge('mysql');
            DB::reconnect('mysql');

            // 2️⃣ Check if the database exists by trying to get its name
            try {
                $dbName = DB::connection()->getDatabaseName();
                if (empty($dbName) || $dbName !== $data['db_database']) {
                    throw new \Exception("Database '{$data['db_database']}' does not exist or cannot be accessed. Please create it and ensure credentials are correct.");
                }
            } catch (\Exception $e) {
                throw new \Exception("Database connection failed: " . $e->getMessage());
            }

            // 3️⃣ Run migrations
            $exitCode = Artisan::call('migrate:fresh', ['--force' => true]);
            if ($exitCode !== 0) {
                throw new \Exception("Database migration failed with exit code: $exitCode");
            }

            // 4️⃣ Optional seeding
            if (config('installer.requirements.seed_database', false)) {
                $seedExitCode = Artisan::call('db:seed', ['--force' => true]);
                if ($seedExitCode !== 0) {
                    throw new \Exception("Database seeding failed with exit code: $seedExitCode");
                }
            }
        }

        // 5️⃣ Link storage if required
        if (config('installer.requirements.link_storage')) {
            try {
                Artisan::call('storage:link');
            } catch (\Exception $e) {
                throw new \Exception("Storage link creation failed: " . $e->getMessage());
            }
        }

        // Save the raw env data just before updating env file
        $this->progress['raw_env_data'] = $data;
        $this->saveProgress();

        // 6️⃣ Write .env immediately with DB credentials
        $this->updateEnvSettings($data);
    }

    /**
     * 
     */
    private function runMailSetup(array $data)
    {
        // Save the raw env data just before updating env file
        $this->progress['raw_env_data'] = $data;
        $this->saveProgress();

        // 6️⃣ Write .env immediately with DB credentials
        $this->updateMailSettings($data);
    }

    /**
     * Update the .env file with provided configuration data.
     *
     * @param array $data Environment configuration data.
     * @throws \Exception If file operations fail.
     * @return void
     */
    private function updateEnvSettings(array $data): void
    {
        $envPath = base_path('.env');

        try {
            $env = File::exists($envPath) ? File::get($envPath) : '';
        } catch (\Exception $e) {
            throw new \Exception("Failed to read .env file: " . $e->getMessage());
        }

        $quoteIfNeeded = fn($value) => preg_match('/\s/', $value ?? '') ? '"' . addslashes($value) . '"' : $value;

        $pairs = [
            'APP_NAME' => $quoteIfNeeded(config('installer.app_name', 'Eii Laravel Installer')),
            'APP_ENV' => config('installer.requirements.environment.production') ? 'production' : 'local',
            'APP_DEBUG' => config('installer.requirements.environment.debug') ? 'true' : 'false',
            'APP_URL' => $data['app_url'] ?? '',
            'DB_CONNECTION' => $data['db_connection'] ?? 'mysql',
            'DB_HOST' => $data['db_host'] ?? '127.0.0.1',
            'DB_PORT' => $data['db_port'] ?? '3306',
            'DB_DATABASE' => $data['db_database'] ?? '',
            'DB_USERNAME' => $data['db_username'] ?? '',
            'DB_PASSWORD' => $data['db_password'] ?? '',
        ];

        foreach ($pairs as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            $env = preg_match($pattern, $env)
                ? preg_replace($pattern, "{$key}={$value}", $env)
                : $env . PHP_EOL . "{$key}={$value}";
        }

        try {
            File::put($envPath, trim($env));
        } catch (\Exception $e) {
            throw new \Exception("Failed to write to .env file: " . $e->getMessage());
        }
    }

    private function updateMailSettings(array $data): void
    {
        $envPath = base_path('.env');

        try {
            $env = File::exists($envPath) ? File::get($envPath) : '';
        } catch (\Exception $e) {
            throw new \Exception("Failed to read .env file: " . $e->getMessage());
        }

        $quoteIfNeeded = fn($value) => preg_match('/\s/', $value ?? '') ? '"' . addslashes($value) . '"' : $value;

        $pairs = [
            'MAIL_MAILER' => $data['mail_mailer'] ?? 'smtp',
            'MAIL_HOST' => $data['mail_host'] ?? '',
            'MAIL_PORT' => $data['mail_port'] ?? '',
            'MAIL_USERNAME' => $data['mail_username'] ?? '',
            'MAIL_PASSWORD' => $data['mail_password'] ?? '',
            'MAIL_FROM_ADDRESS' => $data['mail_from_address'] ?? '',
            'MAIL_FROM_NAME' => $quoteIfNeeded($data['mail_from_name'] ?? ''),
        ];

        foreach ($pairs as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            $env = preg_match($pattern, $env)
                ? preg_replace($pattern, "{$key}={$value}", $env)
                : $env . PHP_EOL . "{$key}={$value}";
        }

        try {
            File::put($envPath, trim($env));
        } catch (\Exception $e) {
            throw new \Exception("Failed to write to .env file: " . $e->getMessage());
        }
    }
}
