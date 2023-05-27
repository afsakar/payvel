<x-filament::page :widget-data="['corporationID' => $record]">
    <h2 class="text-xl">Agreements</h2>
    <livewire:corporation.agreement-table :corporationID="$record" />
    <h2 class="text-xl">Bills</h2>
    <livewire:corporation.bill-table :corporationID="$record" />
    <h2 class="text-xl">Invoices</h2>
    <livewire:corporation.invoice-table :corporationID="$record" />
    <h2 class="text-xl">Revenues</h2>
    <livewire:corporation.revenue-table :corporationID="$record" />
    <h2 class="text-xl">Expenses</h2>
    <livewire:corporation.expense-table :corporationID="$record" />
    <h2 class="text-xl">Waybills</h2>
    <livewire:corporation.waybill-table :corporationID="$record" />
</x-filament::page>
