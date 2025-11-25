<?php

namespace Eii\Installer\Livewire\Install;

use Illuminate\Support\Facades\File;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class EnvironmentSettings extends Component
{
    public bool $isDatabaseRequired = false;
    public string $appUrl = '';
    public string $dbConnection = 'mysql';
    public string $dbHost = '127.0.0.1';
    public string $dbPort = '3306';
    public ?string $dbDatabase = null;
    public ?string $dbUsername = null;
    public ?string $dbPassword = null;

    /**
     * Define validation rules based on configuration requirements.
     *
     * @return array
     */
    protected function rules(): array
    {
        $rules = ['appUrl' => 'required|string'];

        if ($this->isDatabaseRequired) {
            $rules = array_merge($rules, [
                'dbHost' => 'required|regex:/^\S*$/u',
                'dbPort' => 'required|numeric|regex:/^\S*$/u',
                'dbDatabase' => 'required|min:1|regex:/^\S*$/u',
                'dbUsername' => 'required|min:1|regex:/^\S*$/u',
                'dbPassword' => 'nullable|string',
            ]);
        }

        return $rules;
    }

    /**
     * Initialize component with saved progress and configuration.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->isDatabaseRequired = config('installer.requirements.environment.database', false);

        try {
            $progressFile = config('installer.options.progress_file');
            if (File::exists($progressFile)) {
                $progress = json_decode(File::get($progressFile), true);
                $data = $progress['data']['environment'] ?? [];

                $this->appUrl = $data['app_url'] ?? $this->appUrl;
                $this->dbConnection = $data['db_connection'] ?? $this->dbConnection;
                $this->dbHost = $data['db_host'] ?? $this->dbHost;
                $this->dbPort = $data['db_port'] ?? $this->dbPort;
                $this->dbDatabase = $data['db_database'] ?? $this->dbDatabase;
                $this->dbUsername = $data['db_username'] ?? $this->dbUsername;
                $this->dbPassword = $data['db_password'] ?? $this->dbPassword;
            }
        } catch (\Exception $e) {
            $this->dispatch('wizard.error', ['message' => "Failed to load progress: {$e->getMessage()}"]);
            return;
        }

        $this->dispatch('wizard.canProceed');
    }

    /**
     * Validate updated properties and update proceed status.
     *
     * @param string $property Updated property name.
     * @return void
     */
    public function updated(string $property): void
    {
        $this->validateOnly($property);
        if ($this->getErrorBag()->isEmpty($property)) {
            $this->dispatch('wizard.canProceed');
        }
    }

    /**
     * Validate and save environment settings, then proceed to next step.
     *
     * @return void
     */
    #[On('completeStep')]
    public function completeStep(): void
    {
        $this->validate();

        $data = ['app_url' => $this->appUrl];

        if ($this->isDatabaseRequired) {
            $data = array_merge($data, [
                'db_connection' => $this->dbConnection,
                'db_host' => $this->dbHost,
                'db_port' => $this->dbPort,
                'db_database' => $this->dbDatabase,
                'db_username' => $this->dbUsername,
                'db_password' => $this->dbPassword,
            ]);
        }

        $this->dispatch('wizard.stepCompleted', ['data' => $data]);
    }

    /**
     * Render the environment settings view.
     *
     * @return \Illuminate\View\View
     */
    #[Layout('layouts.installer')]
    public function render()
    {
        return view('installer::livewire.install.environment-settings');
    }
}
