<x-filament::page :widget-data="['corporationID' => $record]">
    <h2 class="text-xl">{{ __("agreements.agreements") }}</h2>
    <livewire:corporation.agreement-table :corporationID="$record" />
    <h2 class="text-xl">{{ __("bills.bills") }}</h2>
    <livewire:corporation.bill-table :corporationID="$record" />
    <h2 class="text-xl">{{ __('invoices.invoices') }}</h2>
    <livewire:corporation.invoice-table :corporationID="$record" />
    <h2 class="text-xl">{{ __('revenues.revenues') }}</h2>
    <livewire:corporation.revenue-table :corporationID="$record" />
    <h2 class="text-xl">{{ __('expenses.expenses') }}</h2>
    <livewire:corporation.expense-table :corporationID="$record" />
    <h2 class="text-xl">{{ __('waybills.waybills') }}</h2>
    <livewire:corporation.waybill-table :corporationID="$record" />
</x-filament::page>
