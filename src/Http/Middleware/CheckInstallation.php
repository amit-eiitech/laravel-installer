<?php

namespace Eii\Installer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('installer.run_installer')) {
            return redirect('/');
        }

        $lockFile = config('installer.options.lock_file');

        // If already installed → redirect home
        if (File::exists($lockFile)) {
            return redirect('/');
        }

        return $next($request);
    }
}
