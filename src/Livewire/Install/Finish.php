<?php

namespace Eii\Installer\Livewire\Install;

use Illuminate\Support\Facades\File;
use Livewire\Component;
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
                $this->settings = json_decode(File::get($progressFile), true)['data'] ?? [];
                File::delete($progressFile);
            }

            File::put($lockFile, now()->toDateTimeString());
        } catch (\Exception $e) {
            $this->dispatch('wizard.error', ['message' => "Failed to finalize installation: {$e->getMessage()}"]);
        }
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
