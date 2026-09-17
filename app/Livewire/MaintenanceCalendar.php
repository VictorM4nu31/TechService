<?php

namespace App\Livewire;

use App\Models\MaintenanceSchedule;
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

        $tickets = Ticket::whereBetween('due_date', [$startDate, $endDate])
            ->visibleTo(auth()->user())
            ->with(['equipment'])
            ->get()
            ->map(fn (Ticket $ticket): array => [
                'id' => $ticket->id,
                'title' => $ticket->title,
                'category' => $ticket->category->value,
                'kind' => 'ticket',
                'date' => $ticket->due_date->format('j'),
            ]);

        $schedules = collect();

        if (auth()->user()->hasAnyRole(['Admin', 'Agente'])) {
            $schedules = MaintenanceSchedule::where('is_active', true)
                ->whereBetween('next_run_at', [$startDate, $endDate])
                ->with(['equipment'])
                ->get()
                ->map(fn (MaintenanceSchedule $schedule): array => [
                    'id' => $schedule->id,
                    'title' => $schedule->name,
                    'category' => 'Preventivo',
                    'kind' => 'schedule',
                    'date' => $schedule->next_run_at->format('j'),
                ]);
        }

        $this->events = $tickets
            ->merge($schedules)
            ->groupBy('date')
            ->toArray();
    }

    public function render(): View
    {
        return view('livewire.maintenance-calendar', [
            'monthName' => Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F'),
        ]);
    }
}
