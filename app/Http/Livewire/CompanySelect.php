<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Filament\Forms;
use Illuminate\Contracts\View\View;

class CompanySelect extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $title = 'Custom Page Title';

    public $company_id = null;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('company_id')
                ->label('Company')
                ->reactive()
                ->options(\App\Models\Company::pluck('name', 'id'))
                ->afterStateUpdated(function ($state) {
                    $this->company_id = $state;
                })
                ->searchable()
                ->required(),
        ];
    }

    public function submit()
    {
        $this->validate();
        session()->put('company_id', $this->company_id);
        return redirect()->route('filament.pages.dashboard');
    }

    public function render()
    {
        return view('livewire.company-select');
    }
}
