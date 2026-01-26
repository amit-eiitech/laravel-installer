<?php

namespace Eii\Installer\Livewire\Install;

use Illuminate\Support\Facades\File;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class MailSettings extends Component
{
    public bool $isMailRequired = false;

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
        $rules = [];

        if ($this->isMailRequired) {
            $rules = [
                'mailMailer' => 'required|string',
                'mailHost' => 'required|string',
                'mailPort' => 'required|numeric',
                'mailUsername' => 'required|string',
                'mailPassword' => 'required|string',
                'mailFromAddress' => 'required|email',
                'mailFromName' => 'required|string',
            ];
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
        $this->isMailRequired = config('installer.requirements.environment.mail', false);

        try {
            $progressFile = config('installer.options.progress_file');
            if (File::exists($progressFile)) {
                $progress = json_decode(File::get($progressFile), true);
                $data = $progress['data']['environment'] ?? [];

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

        if ($this->isMailRequired) {
            $data = [
                'mail_mailer' => $this->mailMailer,
                'mail_host' => $this->mailHost,
                'mail_port' => $this->mailPort,
                'mail_username' => $this->mailUsername,
                'mail_password' => $this->mailPassword,
                'mail_from_address' => $this->mailFromAddress,
                'mail_from_name' => $this->mailFromName,
            ];
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
        return view('installer::livewire.install.mail-settings');
    }
}
