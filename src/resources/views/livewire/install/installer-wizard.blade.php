<div x-data="{ showWaitScreen: @entangle('showWaitScreen') }">
    @if (session('installer.error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 shadow-sm rounded-r-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-medium">
                        {{ session('installer.error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div x-show="showWaitScreen" x-cloak>
        <div class="absolute w-full min-h-screen inset-0 bg-black/10 z-50 flex justify-center items-center">
            <div class="bg-white rounded-xl p-8 shadow-lg flex gap-2 items-center">
                <svg class="animate-spin h-6 w-6 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="font-medium text-slate-600">{{ __('Processing...') }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-10 lg:p-12 flex flex-col lg:flex-row gap-8 lg:gap-12 transition-all duration-500">
        <!-- Stepper -->
        <div class="shrink-0 w-full lg:max-w-[280px] border-b lg:border-b-0 lg:border-r pb-8 lg:pb-0 lg:pr-8">
            <div class="flex flex-col items-start space-y-12 relative">
                <!-- Background Track -->
                <div class="absolute left-[15px] top-4 w-0.5 h-[calc(100%-32px)] bg-slate-100 rounded-full"></div>
                <!-- Active Progress -->
                <div id="progress-line" class="absolute left-[15px] top-4 w-0.5 bg-emerald-500 rounded-full transition-all duration-500" style="height: {{ $steps->count() > 1 ? ($currentIndex / ($steps->count() - 1)) * 100 : 0 }}%"></div>

                <!-- Steps -->
                @foreach ($steps as $index => $s)
                    <div class="flex items-center space-x-4 z-10 w-full">
                        @if ($index < $currentIndex)
                            <div id="step-{{ $index }}" class="relative flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500 shadow-sm transition-all duration-300">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @elseif($index === $currentIndex)
                            <div id="step-{{ $index }}" class="relative flex items-center justify-center w-8 h-8 rounded-full bg-slate-800 text-white ring-4 ring-slate-100 shadow-md transition-all duration-300">
                                <span class="text-xs font-bold">{{ $loop->index + 1 }}</span>
                            </div>
                        @else
                            <div id="step-{{ $index }}" class="relative flex items-center justify-center w-8 h-8 rounded-full bg-slate-50 border border-slate-200 text-slate-400 transition-all duration-300">
                                <span class="text-xs font-bold">{{ $index + 1 }}</span>
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold {{ $index === $currentIndex ? 'text-slate-800' : ($index < $currentIndex ? 'text-slate-600' : 'text-slate-400') }} transition-colors duration-300">{{ $s['label'] }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ $s['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Step Content -->
        <div class="flex flex-col grow min-w-0">
            @if (isset($step['component']))
                <div class="mb-8">
                    <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $step['label'] }}</h2>
                    <p class="text-slate-400 text-sm mt-1">{{ $step['description'] }}</p>
                </div>

                <div class="grow py-2">
                    @livewire($step['component'], ['wizard' => $this])
                </div>

                <div class="mt-12 flex items-center justify-end gap-4 border-t pt-8">
                    @if ($skippable)
                        <button class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors px-4" wire:click="skipStep">
                            {{ __('Skip this step') }}
                        </button>
                    @endif

                    @if ($currentIndex === $steps->count() - 1)
                        <button class="btn-primary flex items-center justify-center gap-3 disabled:opacity-70 disabled:cursor-wait" wire:click="completeStep" wire:loading.attr="disabled" wire:target="completeStep">
                            <span wire:loading.remove wire:target="completeStep">
                                {{ __('Finish Installation') }}
                            </span>

                            <span wire:loading.flex wire:target="completeStep" class="items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('Installing...') }}
                            </span>
                        </button>
                    @else
                        <button class="btn-primary flex items-center justify-center gap-3 disabled:opacity-70 disabled:cursor-wait" wire:click="completeStep" wire:loading.attr="disabled" wire:target="completeStep" @disabled(!$canProceed)>
                            <span wire:loading.remove wire:target="completeStep">
                                {{ __('Continue') }}
                            </span>

                            <span wire:loading.flex wire:target="completeStep" class="items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('Loading...') }}
                            </span>
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
