<div class="filament-global-search flex items-center ml-4 rtl:ml-0 rtl:mr-4">
    <x-filament::global-search.start />
    {{ \Filament\Facades\Filament::renderHook('global-search.start') }}

    @if ($this->isEnabled())
        <div class="relative">
            <x-filament::global-search.input />

            @if ($results !== null)
                <x-filament::global-search.results-container :results="$results" />
            @endif
        </div>
    @endif

    <x-filament::global-search.end />
    {{ \Filament\Facades\Filament::renderHook('global-search.end') }}
</div>
