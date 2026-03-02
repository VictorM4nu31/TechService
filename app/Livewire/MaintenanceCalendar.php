<?php

namespace App\Livewire;

use App\Models\Ticket;
use Carbon\Carbon;
use Livewire\Component;

class MaintenanceCalendar extends Component
{
    public $month;

    public $year;

    public $daysInMonth = [];

    public $events = [];

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
        $this->calculateCalendar();
    }

    public function previousMonth()
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
        $this->calculateCalendar();
    }

    public function nextMonth()
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
        $this->calculateCalendar();
    }

    public function calculateCalendar()
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1);
        $daysInMonth = $date->daysInMonth;
        $startDayOfWeek = $date->startOfMonth()->dayOfWeek;

        $this->daysInMonth = [];

        // Fill previous month days
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $this->daysInMonth[] = null;
        }

        // Fill current month days
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $this->daysInMonth[] = $day;
        }

        $this->loadEvents();
    }

    public function loadEvents()
    {
        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $query = Ticket::whereBetween('due_date', [$startDate, $endDate]);

        // Security: Clients only see their own tickets in the calendar
        if (auth()->user()->hasRole('Cliente')) {
            $query->where('created_by', auth()->id());
        }

        $this->events = $query->with(['status', 'equipment'])
            ->get()
            ->groupBy(function ($ticket) {
                return $ticket->due_date->format('j');
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.maintenance-calendar', [
            'monthName' => Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F'),
        ]);
    }
}
