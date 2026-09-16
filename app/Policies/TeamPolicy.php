<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Agente']);
    }

    public function view(User $user, Team $team): bool
    {
        return $user->hasAnyRole(['Admin', 'Agente']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function update(User $user, Team $team): bool
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, Team $team): bool
    {
        return $user->hasRole('Admin');
    }
}
