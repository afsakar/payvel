<?php

namespace App\Providers;

use App\Models\Company;
use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\UserMenuItem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerUserMenuItems([
                UserMenuItem::make()
                    ->label('Change Company (' . Company::find(session('company_id'))->name . ')')
                    ->url(route('company.change'))
                    ->icon('heroicon-s-refresh'),
                // ...
            ]);


            Filament::registerNavigationItems([
                NavigationItem::make(Company::find(session()->get('company_id'))->name)
                    ->icon('heroicon-s-office-building')
                    ->sort(-2),
            ]);
        });
    }
}
