<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->hasAnyRole(['Admin', 'Agente'])) {
            return true;
        }

        return $ticket->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->hasAnyRole(['Admin', 'Agente'])) {
            return true;
        }

        return $ticket->created_by === $user->id && $ticket->status === TicketStatus::Abierto;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('Admin');
    }

    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('Admin');
    }

    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('Admin');
    }
}
