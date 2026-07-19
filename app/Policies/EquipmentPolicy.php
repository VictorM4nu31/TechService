<?php

namespace App\Policies;

use App\Models\Equipment;
use App\Models\User;

class EquipmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Equipment $equipment): bool
    {
        return $user->hasRole('Admin') || $equipment->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Equipment $equipment): bool
    {
        return $user->hasRole('Admin') || $equipment->user_id === $user->id;
    }

    public function delete(User $user, Equipment $equipment): bool
    {
        return $user->hasRole('Admin') || $equipment->user_id === $user->id;
    }
}
