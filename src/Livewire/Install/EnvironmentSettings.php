<?php

namespace Eii\Installer\Livewire\Install;

use Illuminate\Support\Facades\File;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class EnvironmentSettings extends Component
{
    public bool $isDatabaseRequired = false;
    public bool $isMailRequired = false;

    public string $appUrl = '';
    public string $dbConnection = 'mysql';
    public string $dbHost = '127.0.0.1';
    public string $dbPort = '3306';
    public ?string $dbDatabase = null;
    public ?string $dbUsername = null;
    public ?string $dbPassword = null;
    public string $mailMailer = 'smtp';
    public string $mailHost = '127.0.0.1';
    public string $mailPort = '587';
    public ?string $mailUsername = null;
    public ?string $mailPassword = null;
    public ?string $mailFromAddress = null;
    public ?string $mailFromName = null;

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

        if ($this->isMailRequired) {
            $rules = array_merge($rules, [
                'mailMailer' => 'required|string',
                'mailHost' => 'required|string',
                'mailPort' => 'required|numeric',
                'mailUsername' => 'required|string',
                'mailPassword' => 'required|string',
                'mailFromAddress' => 'required|email',
                'mailFromName' => 'required|string',
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
        $this->isMailRequired = config('installer.requirements.environment.mail', false);

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
                $this->mailMailer = $data['mail_mailer'] ?? $this->mailMailer;
                $this->mailHost = $data['mail_host'] ?? $this->mailHost;
                $this->mailPort = $data['mail_port'] ?? $this->mailPort;
                $this->mailUsername = $data['mail_username'] ?? $this->mailUsername;
                $this->mailPassword = $data['mail_password'] ?? $this->mailPassword;
                $this->mailFromAddress = $data['mail_from_address'] ?? $this->mailFromAddress;
                $this->mailFromName = $data['mail_from_name'] ?? $this->mailFromName;
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
        try {
            $this->validateOnly($property);
            $this->dispatch('wizard.canProceed');
        } catch (\Exception $e) {
            $this->dispatch('wizard.cannotProceed');
            $this->dispatch('wizard.error', ['message' => "Validation failed: {$e->getMessage()}"]);
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
        try {
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

            if ($this->isMailRequired) {
                $data = array_merge($data, [
                    'mail_mailer' => $this->mailMailer,
                    'mail_host' => $this->mailHost,
                    'mail_port' => $this->mailPort,
                    'mail_username' => $this->mailUsername,
                    'mail_password' => $this->mailPassword,
                    'mail_from_address' => $this->mailFromAddress,
                    'mail_from_name' => $this->mailFromName,
                ]);
            }

            $this->dispatch('wizard.stepCompleted', ['data' => $data]);
        } catch (\Exception $e) {
            $this->dispatch('wizard.cannotProceed');
            $this->dispatch('wizard.error', ['message' => "Failed to complete step: {$e->getMessage()}"]);
        }
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
