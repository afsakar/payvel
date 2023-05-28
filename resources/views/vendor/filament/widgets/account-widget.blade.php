<x-filament::widget class="filament-account-widget">
    <x-filament::card>
        @php
            $user = \Filament\Facades\Filament::auth()->user();
        @endphp

        @php
            $company = \App\Models\Company::where('id', session()->get('company_id'))->first();
        @endphp

        <div class="flex items-center justify-between">
            <div class="h-12 flex items-center justify-between space-x-4 rtl:space-x-reverse">
                <x-filament::user-avatar :user="$user" />

                <div>
                    <h2 class="text-lg sm:text-xl font-bold tracking-tight">
                        {{ __('filament::widgets/account-widget.welcome', ['user' => \Filament\Facades\Filament::getUserName($user)]) }}
                    </h2>

                    <form action="{{ route('filament.auth.logout') }}" method="post" class="text-sm">
                        @csrf

                        <button type="submit" @class([
                            'text-gray-600 hover:text-primary-500 outline-none focus:underline',
                            'dark:text-gray-300 dark:hover:text-primary-500' => config(
                                'filament.dark_mode'),
                        ])>
                            {{ __('filament::widgets/account-widget.buttons.logout.label') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="text-right">
                <div class="font-xs text-gray-5600 dark:text-gray-300">
                    {{ __('general.selected_company') }}
                </div>
                <h2 class="text-lg sm:text-xl font-bold tracking-tight">
                    {{ $company->name }}
                </h2>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
