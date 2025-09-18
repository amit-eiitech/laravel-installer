<?php

namespace Eii\Installer\Livewire\Install;

use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class Welcome extends Component
{
    public string $php;
    public ?string $extensions = null;
    public string $appName;
    public bool $isDatabaseRequired = false;
    public bool $isMailRequired = false;

    /**
     * Initialize component with configuration data.
     *
     * @return void
     */
    public function mount(): void
    {
        try {
            $this->php = config('installer.requirements.php', '8.0');
            $this->extensions = implode(', ', config('installer.requirements.extensions', [])) ?: null;
            $this->appName = config('installer.app_name', 'Laravel Installer');
            $this->isDatabaseRequired = config('installer.requirements.environment.database', false);
            $this->isMailRequired = config('installer.requirements.environment.mail', false);

            $this->dispatch('wizard.canProceed');
        } catch (\Exception $e) {
            Log::error('Welcome mount failed: ' . $e->getMessage());
            $this->dispatch('wizard.cannotProceed');
            $this->dispatch('wizard.error', ['message' => "Failed to load welcome step: {$e->getMessage()}"]);
        }
    }

    /**
     * Proceed to the next step.
     *
     * @return void
     */
    #[On('completeStep')]
    public function completeStep(): void
    {
        $this->dispatch('wizard.stepCompleted');
    }

    /**
     * Render the welcome view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('installer::livewire.install.welcome');
    }
}
