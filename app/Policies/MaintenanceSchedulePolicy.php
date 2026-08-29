<?php

namespace App\Policies;

use App\Models\MaintenanceSchedule;
use App\Models\User;

class MaintenanceSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Agente']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Agente']);
    }

    public function update(User $user, MaintenanceSchedule $maintenanceSchedule): bool
    {
        return $user->hasAnyRole(['Admin', 'Agente']);
    }

    public function delete(User $user, MaintenanceSchedule $maintenanceSchedule): bool
    {
        return $user->hasAnyRole(['Admin', 'Agente']);
    }
}
