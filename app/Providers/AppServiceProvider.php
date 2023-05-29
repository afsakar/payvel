<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\UserMenuItem;
use Illuminate\Support\Facades\Gate;

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
            if (Company::find(session()->get('company_id'))) {
                Filament::registerUserMenuItems([
                    UserMenuItem::make()
                        ->label(__('general.change_company', ['company' => Company::find(session()->get('company_id'))->name]))
                        ->url(route('company.change'))
                        ->icon('heroicon-o-refresh'),
                ]);

                Filament::registerNavigationItems([
                    NavigationItem::make(Company::find(session()->get('company_id'))->name)
                        ->icon('heroicon-o-office-building')
                        ->sort(-2),
                ]);
                Filament::registerNavigationGroups([
                    NavigationGroup::make()
                        ->label(__('general.settings'))
                        ->icon('heroicon-o-cog')
                        ->collapsed(),
                ]);
            }
        });

        Gate::define('use-translation-manager', function (?User $user) {
            // Your authorization logic
            return $user !== null;
        });
    }
}
