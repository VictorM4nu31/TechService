<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MaintenanceSchedule;
use App\Models\Ticket;
use App\Models\Status;
use App\Models\Priority;
use App\Models\Category;
use App\Models\User;

class GeneratePreventiveWorkOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-preventive-maintenance';
    protected $description = 'Generate preventive maintenance work orders (tickets) based on active schedules';

    public function handle()
    {
        $today = now();
        $schedules = MaintenanceSchedule::where('is_active', true)
            ->where(function ($query) use ($today) {
                $query->whereNull('next_run_at')
                    ->orWhere('next_run_at', '<=', $today);
            })
            ->get();

        if ($schedules->isEmpty()) {
            $this->info('No maintenance schedules due for generation.');
            return 0;
        }

        foreach ($schedules as $schedule) {
            $this->generateWorkOrder($schedule);
        }

        $this->info("Generated {$schedules->count()} preventive maintenance work orders.");
        return 1;
    }

    protected function generateWorkOrder(MaintenanceSchedule $schedule)
    {
        $status = Status::where('name', 'Abierto')->orWhere('name', 'Open')->first();
        $priority = Priority::where('name', 'Media')->orWhere('name', 'Medium')->first();
        $category = Category::where('name', 'Mantenimiento Preventivo')->orWhere('name', 'Preventive')->first() 
                    ?? Category::first();

        Ticket::create([
            'title' => "Mantenimiento Preventivo: {$schedule->name} - {$schedule->equipment->name}",
            'description' => $schedule->description ?? "Tarea de mantenimiento programada para el equipo {$schedule->equipment->name}.",
            'status_id' => $status?->id,
            'priority_id' => $priority?->id,
            'category_id' => $category?->id,
            'equipment_id' => $schedule->equipment_id,
            'due_date' => now()->addDays(2), // Give 2 days to complete
            'created_by' => User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->first()?->id,
            'maintenance_type' => 'Preventivo',
        ]);

        $schedule->update([
            'last_run_at' => now(),
            'next_run_at' => now()->addDays($schedule->frequency_days),
        ]);
    }
}
