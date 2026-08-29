<?php

namespace App\Livewire;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class MaintenanceCalendar extends Component
{
    public $month;

    public $year;

    public $daysInMonth = [];

    public $events = [];

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year = now()->year;
        $this->calculateCalendar();
    }

    public function previousMonth(): void
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
        $this->calculateCalendar();
    }

    public function nextMonth(): void
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
        $this->calculateCalendar();
    }

    public function goToToday(): void
    {
        $this->month = now()->month;
        $this->year = now()->year;
        $this->calculateCalendar();
    }

    public function calculateCalendar(): void
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1);
        $daysInMonth = $date->daysInMonth;
        $startDayOfWeek = $date->startOfMonth()->dayOfWeek;

        $this->daysInMonth = [];

        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $this->daysInMonth[] = null;
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $this->daysInMonth[] = $day;
        }

        $this->loadEvents();
    }

    public function loadEvents(): void
    {
        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $query = Ticket::whereBetween('due_date', [$startDate, $endDate])->visibleTo(auth()->user());

        $this->events = $query->with(['equipment'])
            ->get()
            ->groupBy(function ($ticket) {
                return $ticket->due_date->format('j');
            })
            ->toArray();
    }

    public function render(): View
    {
        return view('livewire.maintenance-calendar', [
            'monthName' => Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F'),
        ]);
    }
}
