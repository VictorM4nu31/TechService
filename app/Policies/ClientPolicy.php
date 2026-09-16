<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Agente']);
    }

    public function view(User $user, Client $client): bool
    {
        return $user->hasAnyRole(['Admin', 'Agente']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Agente']);
    }

    public function update(User $user, Client $client): bool
    {
        return $user->hasAnyRole(['Admin', 'Agente']);
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->hasRole('Admin');
    }
}
