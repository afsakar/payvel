@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ __('filament::layout.direction') ?? 'ltr' }}"
    class="filament js-focus-visible min-h-screen antialiased">

<head>
    {{ \Filament\Facades\Filament::renderHook('head.start') }}

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @foreach (\Filament\Facades\Filament::getMeta() as $tag)
        {{ $tag }}
    @endforeach

    @if ($favicon = config('filament.favicon'))
        <link rel="icon" href="{{ $favicon }}">
    @endif

    <title>{{ $title ? "{$title} - " : null }} {{ config('filament.brand') }}</title>

    {{ \Filament\Facades\Filament::renderHook('styles.start') }}

    <style>
        [x-cloak=""],
        [x-cloak="x-cloak"],
        [x-cloak="1"] {
            display: none !important;
        }

        @media (max-width: 1023px) {
            [x-cloak="-lg"] {
                display: none !important;
            }
        }

        @media (min-width: 1024px) {
            [x-cloak="lg"] {
                display: none !important;
            }
        }

        :root {
            --sidebar-width: {{ config('filament.layout.sidebar.width') ?? '20rem' }};
            --collapsed-sidebar-width: {{ config('filament.layout.sidebar.collapsed_width') ?? '5.4rem' }};
        }
    </style>

    @livewireStyles

    @if (filled($fontsUrl = config('filament.google_fonts')))
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="{{ $fontsUrl }}" rel="stylesheet" />
    @endif

    @foreach (\Filament\Facades\Filament::getStyles() as $name => $path)
        @if (\Illuminate\Support\Str::of($path)->startsWith(['http://', 'https://']))
            <link rel="stylesheet" href="{{ $path }}" />
        @elseif (\Illuminate\Support\Str::of($path)->startsWith('<'))
            {!! $path !!}
        @else
            <link rel="stylesheet"
                href="{{ route('filament.asset', [
                    'file' => "{$name}.css",
                ]) }}" />
        @endif
    @endforeach

    {{ \Filament\Facades\Filament::getThemeLink() }}

    {{ \Filament\Facades\Filament::renderHook('styles.end') }}

    @if (config('filament.dark_mode'))
        <script>
            const theme = localStorage.getItem('theme')

            if ((theme === 'dark') || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            }
        </script>
    @endif

    {{ \Filament\Facades\Filament::renderHook('head.end') }}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body @class([
    'filament-body min-h-screen bg-gray-100 text-gray-900 overflow-y-auto',
    'dark:text-gray-100 dark:bg-gray-900' => config('filament.dark_mode'),
])>
    {{ \Filament\Facades\Filament::renderHook('body.start') }}

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div @class([
        'flex items-center justify-center min-h-screen bg-gray-100 text-gray-900 filament-login-page',
        'dark:bg-gray-900 dark:text-white' => config('filament.dark_mode'),
    ])>
        <div class="px-6 -mt-16 md:mt-0 md:px-2 max-w-md space-y-8 w-screen">
            <div @class(['filament-brand text-xl flex items-center justify-center']) x-data="{ mode: localStorage.getItem('theme') }"
                x-on:dark-mode-toggled.window="mode = $event.detail">
                <img x-show="mode === 'light'" src="{{ asset('/images/payvel.svg') }}" alt="Logo" class="h-14"
                    loading="lazy">
                <img x-show="mode === 'dark' || !mode" src="{{ asset('/images/payvel-dark.svg') }}" alt="Logo"
                    class="h-14" loading="lazy">
            </div>
            <form method="POST" action="{{ route('login') }}" @class([
                'p-8 space-y-8 bg-white/50 backdrop-blur-xl border border-gray-200 shadow-2xl rounded-2xl relative',
                'dark:bg-gray-900/50 dark:border-gray-700' => config('filament.dark_mode'),
            ])>
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('general.email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                        :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('general.password')" />

                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                        autocomplete="current-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="mt-4 flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                            name="remember">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('general.remember_me') }}</span>
                    </label>

                    <x-primary-button class="ml-3">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>

                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>
        @livewire('notifications')
    </div>

    {{ \Filament\Facades\Filament::renderHook('scripts.start') }}

    @livewireScripts

    <script>
        window.filamentData = @json(\Filament\Facades\Filament::getScriptData());
    </script>

    @foreach (\Filament\Facades\Filament::getBeforeCoreScripts() as $name => $path)
        @if (\Illuminate\Support\Str::of($path)->startsWith(['http://', 'https://']))
            <script defer src="{{ $path }}"></script>
        @elseif (\Illuminate\Support\Str::of($path)->startsWith('<'))
            {!! $path !!}
        @else
            <script defer src="{{ route('filament.asset', [
                'file' => "{$name}.js",
            ]) }}"></script>
        @endif
    @endforeach

    @stack('beforeCoreScripts')

    <script defer
        src="{{ route('filament.asset', [
            'id' => Filament\get_asset_id('app.js'),
            'file' => 'app.js',
        ]) }}">
    </script>

    @if (config('filament.broadcasting.echo'))
        <script defer
            src="{{ route('filament.asset', [
                'id' => Filament\get_asset_id('echo.js'),
                'file' => 'echo.js',
            ]) }}">
        </script>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                window.Echo = new window.EchoFactory(@js(config('filament.broadcasting.echo')))

                window.dispatchEvent(new CustomEvent('EchoLoaded'))
            })
        </script>
    @endif

    @foreach (\Filament\Facades\Filament::getScripts() as $name => $path)
        @if (\Illuminate\Support\Str::of($path)->startsWith(['http://', 'https://']))
            <script defer src="{{ $path }}"></script>
        @elseif (\Illuminate\Support\Str::of($path)->startsWith('<'))
            {!! $path !!}
        @else
            <script defer src="{{ route('filament.asset', [
                'file' => "{$name}.js",
            ]) }}"></script>
        @endif
    @endforeach

    @stack('scripts')

    {{ \Filament\Facades\Filament::renderHook('scripts.end') }}

    {{ \Filament\Facades\Filament::renderHook('body.end') }}
</body>

</html>
