<?php

namespace Eii\Installer\Livewire\Install;

use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\Attributes\On;
use Symfony\Component\HttpFoundation\StreamedResponse;


class Finish extends Component
{
    public array $settings = [];

    /**
     * Initialize component, load settings, and finalize installation.
     *
     * @return void
     */
    public function mount(): void
    {
        try {
            $lockFile = config('installer.options.lock_file');
            $progressFile = config('installer.options.progress_file');

            if (File::exists($progressFile)) {
                $settings = json_decode(File::get($progressFile), true)['data'] ?? [];
                $this->settings = $this->maskSensitiveData($settings);

                try {
                    File::delete($progressFile);
                } catch (\Exception $e) {
                    $this->dispatch('wizard.error', ['message' => "Failed to delete progress file: {$e->getMessage()}"]);
                    return;
                }
            }

            try {
                File::put($lockFile, now()->toDateTimeString());
            } catch (\Exception $e) {
                $this->dispatch('wizard.error', ['message' => "Failed to create lock file: {$e->getMessage()}"]);
                return;
            }

            $this->dispatch('wizard.canProceed');
        } catch (\Exception $e) {
            $this->dispatch('wizard.error', ['message' => "Failed to finalize installation: {$e->getMessage()}"]);
        }
    }

    /**
     * Finish the wizard and redirect to the config->redirect url
     *
     */
    #[On('completeStep')]
    public function completeStep()
    {
        $redirect_url = config('installer.options.redirect_after_install', "/");
        return redirect($redirect_url);
    }

    /**
     * Download installation settings as a text file.
     *
     * @return StreamedResponse
     */
    public function downloadSettings(): StreamedResponse
    {
        try {
            $content = "Saved Installation Settings\n" . str_repeat("=", 40) . "\n\n";
            foreach ($this->settings as $step => $data) {
                $content .= strtoupper($step) . ":\n";
                $content .= $this->formatData($data, 1);
                $content .= "\n";
            }

            $filename = 'installation_settings_' . now()->format('Y-m-d_H-i-s') . '.txt';

            return response()->streamDownload(function () use ($content) {
                echo $content;
            }, $filename, [
                'Content-Type' => 'text/plain',
                'Cache-Control' => 'no-store, no-cache',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('wizard.error', ['message' => "Failed to download settings: {$e->getMessage()}"]);
            return response()->streamDownload(function () use ($e) {
                echo "Error: {$e->getMessage()}";
            }, 'error.txt', ['Content-Type' => 'text/plain']);
        }
    }

    /**
     * Recursively mask sensitive values before assigning data to Livewire public properties.
     *
     * @param mixed $data Data to sanitize.
     * @return mixed
     */
    protected function maskSensitiveData($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->maskSensitiveData($value);
                continue;
            }

            if ($this->isSensitiveKey((string) $key)) {
                $data[$key] = ($value !== null && $value !== '') ? '********' : $value;
            }
        }

        return $data;
    }

    /**
     * Determine whether a settings key contains sensitive data.
     *
     * @param string $key Settings key.
     * @return bool
     */
    protected function isSensitiveKey(string $key): bool
    {
        $key = strtolower($key);

        return str_contains($key, 'password')
            || str_contains($key, 'secret')
            || str_contains($key, 'token')
            || str_contains($key, 'api_key')
            || str_contains($key, 'apikey');
    }

    /**
     * Recursively format settings data for text output.
     *
     * @param mixed $data Data to format.
     * @param int $indentLevel Indentation level for formatting.
     * @return string
     */
    protected function formatData($data, int $indentLevel = 0): string
    {
        $output = '';
        $indent = str_repeat('  ', $indentLevel);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $output .= "{$indent}{$key}:\n";
                $output .= $this->formatData($value, $indentLevel + 1);
            } else {
                $value = is_bool($value) ? ($value ? 'true' : 'false') : ($value ?? 'null');
                $output .= "{$indent}{$key}: {$value}\n";
            }
        }

        return $output;
    }

    /**
     * Render the finish view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('installer::livewire.install.finish');
    }
}
