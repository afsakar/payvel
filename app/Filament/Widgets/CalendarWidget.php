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
        $events = Event::select('id', 'title', 'description', 'start', 'end', 'reminder')->get()->toArray();

        foreach ($events as $key => $event) {
            $events[$key]['color'] = '#f59e0b';
            $events[$key]['backgroundColor'] = '#000000';
        }

        return $events;
    }
}
