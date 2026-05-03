<div class="flex flex-col items-center justify-center text-center p-12">
    <h2 class="text-2xl font-bold mb-4">🎉 {{ __('Installation Complete!') }}</h2>
    <p class="text-gray-600 mb-12">
        {{ __('Your application has been successfully installed and configured.') }}
    </p>

    <div class="flex gap-4">
        <button class="px-6 py-2 bg-black text-white rounded-lg cursor-pointer active:scale-95 transition-all duration-200" wire:click="downloadSettings">
            {{ __('Save Settings') }}
        </button>
        <a href="https://github.com/amit-eiitech/laravel-installer" target="_blank" class="px-6 py-2 bg-white text-gray-800 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 active:scale-95 transition-all duration-200 flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
            {{ __('Star on GitHub') }}
        </a>
    </div>
</div>
