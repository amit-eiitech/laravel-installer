<?php

namespace Eii\Installer\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class InstallController extends Controller
{
    /**
     * Start installation wizard
     */
    public function start()
    {
        $progressFile = config('installer.options.progress_file');

        // If progress file exists → resume where left off
        if (File::exists($progressFile)) {
            $progress = json_decode(File::get($progressFile), true);

            if (!empty($progress['current_step'])) {
                return redirect()->route('install.step', $progress['current_step']);
            }
        }

        // If first time → create fresh progress file
        $initialData = [
            'current_step' => 'welcome',
            'data' => [],
        ];

        File::put($progressFile, json_encode($initialData, JSON_PRETTY_PRINT));
        return redirect()->route('install.step', 'welcome');
    }
}
