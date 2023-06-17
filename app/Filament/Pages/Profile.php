<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.profile';

    public $user = null;

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->user = auth()->user();
    }

    /**
     * @return bool
     */
    protected static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected function getTitle(): string
    {
        return static::$title ?? __('profile.profile');
    }
}
