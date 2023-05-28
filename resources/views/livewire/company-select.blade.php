<div @class([
    'flex items-center justify-center min-h-screen bg-gray-100 text-gray-900 filament-login-page',
    'dark:bg-gray-900 dark:text-white' => config('filament.dark_mode'),
])>
    <div class="px-6 -mt-16 md:mt-0 md:px-2 max-w-md space-y-8 w-screen">
        <form wire:submit.prevent="submit" @class([
            'p-8 space-y-8 bg-white/50 backdrop-blur-xl border border-gray-200 shadow-2xl rounded-2xl relative',
            'dark:bg-gray-900/50 dark:border-gray-700' => config('filament.dark_mode'),
        ])>
            <div>
                <h2 class="font-bold tracking-tight text-center text-2xl">
                    {{ __('general.company_change') }}
                </h2>
            </div>

            {{ $this->form }}

            <x-filament::button type="submit" class="w-full">
                {{ __('general.continue') }}
            </x-filament::button>
        </form>
    </div>
    @livewire('notifications')
</div>
