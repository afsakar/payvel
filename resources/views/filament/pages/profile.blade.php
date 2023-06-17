<x-filament::page>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
</x-filament::page>
