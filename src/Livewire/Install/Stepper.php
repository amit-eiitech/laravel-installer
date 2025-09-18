<?php

namespace Eii\Installer\Livewire\Install;

use Illuminate\Support\Facades\Route;
use Livewire\Attributes\On;
use Livewire\Component;

class Stepper extends Component
{
    public $steps = [
        ['label' => 'Requirements Check', 'description' => 'Checking server requirements', 'route' => 'install'],
        ['label' => 'Environmental Settings', 'description' => 'Checking server requirements', 'route' => 'install.environment'],
        ['label' => 'Database', 'description' => 'Database migration and admin user creation', 'route' => 'install.database'],
        ['label' => 'Create Admin', 'description' => 'Database migration and admin user creation', 'route' => 'install.admin'],
        ['label' => 'Mail Settings', 'description' => 'Setup your email', 'route' => 'install.mail'],
        ['label' => 'Finished', 'description' => 'Finalizing installation', 'route' => 'install.finished']
    ];

    public $currentStep = 1;

    public function mount()
    {
        $currentRoute = Route::current()->getName();
        foreach ($this->steps as $index => $step) {
            if ($step['route'] === $currentRoute) {
                $this->currentStep = $index + 1;
            }
        }
    }

    #[On('set-current-step')]
    public function setCurrentStep($step)
    {
        dd('ss');
        $this->currentStep = $step;
    }

    public function render()
    {
        return view('installer::livewire.install.stepper');
    }
}
