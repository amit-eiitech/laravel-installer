<?php

namespace Eii\Installer\Livewire\Install;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\On;
use Livewire\Component;

class ServerRequirements extends Component
{
    public array $requirements = [];

    /**
     * Initialize component with server requirements check.
     *
     * @return void
     */
    public function mount(): void
    {
        try {
            $requiredPhp = config('installer.requirements.php', '8.0');
            $requiredExtensions = config('installer.requirements.extensions', []);
            $requiredPermissions = config('installer.requirements.permissions', []);

            $this->requirements = [
                'php' => [
                    'current' => PHP_VERSION,
                    'required' => $requiredPhp,
                    'passed' => version_compare(PHP_VERSION, $requiredPhp, '>='),
                ],
                'extensions' => collect($requiredExtensions)->mapWithKeys(fn($ext) => [$ext => extension_loaded($ext)])->toArray(),
                'permissions' => collect($requiredPermissions)->mapWithKeys(function ($status, $path) {
                    $fullPath = base_path($path);
                    return [
                        $path => [
                            'exists' => File::exists($fullPath),
                            'writable' => File::isWritable($fullPath),
                        ]
                    ];
                })->toArray(),
            ];

            $this->dispatch($this->checkAllPassed() ? 'wizard.canProceed' : 'wizard.cannotProceed');
        } catch (\Exception $e) {
            $this->dispatch('wizard.cannotProceed');
            $this->dispatch('wizard.error', ['message' => "Failed to check server requirements: {$e->getMessage()}"]);
        }
    }

    /**
     * Proceed to the next step with requirements data.
     *
     * @return void
     */
    #[On('completeStep')]
    public function completeStep(): void
    {
        try {
            $this->dispatch('wizard.stepCompleted', ['data' => $this->requirements]);
        } catch (\Exception $e) {
            $this->dispatch('wizard.cannotProceed');
            $this->dispatch('wizard.error', ['message' => "Failed to complete requirements step: {$e->getMessage()}"]);
        }
    }

    /**
     * Check if all server requirements are met.
     *
     * @return bool
     */
    protected function checkAllPassed(): bool
    {
        $phpOk = $this->requirements['php']['passed'] ?? false;
        $extsOk = collect($this->requirements['extensions'])->every(fn($loaded) => $loaded === true);
        $permsOk = collect($this->requirements['permissions'])->every(fn($info) => $info['exists'] && $info['writable']);

        return $phpOk && $extsOk && $permsOk;
    }

    /**
     * Render the server requirements view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('installer::livewire.install.server-requirements');
    }
}
