@if (filled($brand = config('filament.brand')))
    <div @class([
        'filament-brand text-xl font-bold tracking-tight',
        'dark:text-white' => config('filament.dark_mode'),
    ]) x-data="{ mode: localStorage.getItem('theme') }" x-on:dark-mode-toggled.window="mode = $event.detail">
        <img x-show="mode === 'light'" src="{{ asset('/images/payvel.svg') }}" alt="Logo" class="h-10" loading="lazy">
        <img x-show="mode === 'dark'" src="{{ asset('/images/payvel-dark.svg') }}" alt="Logo" class="h-10"
            loading="lazy">
    </div>
@endif
