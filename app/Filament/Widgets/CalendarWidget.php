<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Saade\FilamentFullCalendar\Widgets\Concerns\CantManageEvents;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    use CantManageEvents;

    protected array $fullCalendarConfig;

    public function __construct()
    {
        $this->fullCalendarConfig = [
            'locale' => app()->getLocale(),
        ];
    }

    /**
     * Return events that should be rendered statically on calendar.
     */
    public function getViewData(): array
    {
        return Event::select('id', 'title', 'description', 'start', 'end', 'reminder')->get()->toArray();
    }
}
