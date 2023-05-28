<x-filament::page>
    <div
        class="filament-forms-card-component p-6 bg-white rounded-xl text-2xl border border-gray-300 dark:border-gray-600 dark:bg-gray-800 flex items-center justify-between">
        <div class="">
            {{ __('accounts.balance') }}
        </div>
        <div class="font-bold">
            {{ $account->balance }}
        </div>
    </div>
    <h2 class="text-xl">{{ __('revenues.revenues') }}</h2>
    <livewire:account.revenue-table :accountID="$record" />
    <h2 class="text-xl">{{ __('expenses.expenses') }}</h2>
    <livewire:account.expense-table :accountID="$record" />
    <h2 class="text-xl">{{ __('transactions.transactions') }}</h2>
    <livewire:account.transaction-table :accountID="$record" />
    <h2 class="text-xl">{{ __('checks.checks') }}</h2>
    <livewire:account.check-table :accountID="$record" />
</x-filament::page>
