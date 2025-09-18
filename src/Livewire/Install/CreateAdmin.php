<?php

namespace Eii\Installer\Livewire\Install;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateAdmin extends Component
{
    public ?string $name = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;

    /**
     * Define validation rules for admin user creation.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ];
    }

    /**
     * Initialize component and enable proceeding.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->dispatch('wizard.canProceed');
    }

    /**
     * Validate updated properties.
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
     * Create admin user and proceed to next step.
     *
     * @return void
     */
    #[On('completeStep')]
    public function completeStep(): void
    {
        $this->validate();

        try {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ];

            $this->dispatch('wizard.stepCompleted', ['data' => $data]);
        } catch (\Exception $e) {
            $this->dispatch('wizard.error', ['message' => "Failed to create admin user: {$e->getMessage()}"]);
        }
    }

    /**
     * Render the create admin view.
     *
     * @return \Illuminate\View\View
     */
    #[Layout('layouts.installer')]
    public function render()
    {
        return view('installer::livewire.install.create-admin');
    }
}
