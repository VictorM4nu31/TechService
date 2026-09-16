<?php

namespace App\Console\Commands;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\MaintenanceSchedule;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;

class GeneratePreventiveWorkOrders extends Command
{
    protected $signature = 'app:generate-preventive-maintenance';

    protected $description = 'Generate preventive maintenance work orders (tickets) based on active schedules';

    public function handle(): int
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

            return Command::SUCCESS;
        }

        $admin = User::role('Admin')->first();

        if ($admin === null) {
            $this->error('No se encontró un usuario con rol Admin. No se generaron órdenes de trabajo.');

            return Command::FAILURE;
        }

        foreach ($schedules as $schedule) {
            $this->generateWorkOrder($schedule, $admin->id);
        }

        $this->info("Generated {$schedules->count()} preventive maintenance work orders.");

        return Command::SUCCESS;
    }

    protected function generateWorkOrder(MaintenanceSchedule $schedule, int $adminId): void
    {
        Ticket::create([
            'title' => "Mantenimiento Preventivo: {$schedule->name} - {$schedule->equipment->name}",
            'description' => $schedule->description ?? "Tarea de mantenimiento programada para el equipo {$schedule->equipment->name}.",
            'status' => TicketStatus::Abierto,
            'priority' => TicketPriority::Media,
            'category' => TicketCategory::Preventivo,
            'equipment_id' => $schedule->equipment_id,
            'due_date' => now()->addDays(2),
            'created_by' => $adminId,
            'maintenance_type' => 'Preventivo',
        ]);

        $schedule->update([
            'last_run_at' => now(),
            'next_run_at' => now()->addDays($schedule->frequency_days),
        ]);
    }
}
