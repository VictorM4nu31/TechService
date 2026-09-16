<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\Activity;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TicketService
{
    public function createTicket(array $data): Ticket
    {
        $ticket = Ticket::create($data);

        $this->logActivity($ticket, 'created', 'creó el ticket');

        $this->invalidateCache($ticket);

        return $ticket;
    }

    public function assignTicket(Ticket $ticket, int $userId): void
    {
        $user = User::find($userId);
        abort_unless($user !== null, 422);

        $ticket->update(['assigned_to' => $userId]);

        $this->logActivity($ticket, 'assigned', "asignó el ticket a {$user->name}", ['assigned_to' => $userId]);

        $this->invalidateCache($ticket);
    }

    public function addComment(Ticket $ticket, string $content, array $attachments = []): Comment
    {
        $comment = $ticket->comments()->create([
            'user_id' => Auth::id(),
            'content' => $content,
        ]);

        foreach ($attachments as $attachment) {
            $comment->addMedia($attachment)->toMediaCollection('attachments');
        }

        $this->logActivity($ticket, 'commented', 'comentó en el ticket', ['comment_id' => $comment->id]);

        return $comment;
    }

    public function updateStatus(Ticket $ticket, TicketStatus $newStatus): void
    {
        $oldStatus = $ticket->status->label();
        $ticket->update(['status' => $newStatus]);

        $this->logActivity($ticket, 'status_updated', "cambió el estado de {$oldStatus} a {$newStatus->label()}", [
            'old_status' => $ticket->getOriginal('status'),
            'new_status' => $newStatus->value,
        ]);

        $this->invalidateCache($ticket);
    }

    public function resolveTicket(Ticket $ticket): void
    {
        $this->updateStatus($ticket, TicketStatus::Cerrado);
        $this->logActivity($ticket, 'resolved', 'resolvió el ticket');
    }

    public function logActivity(Ticket $ticket, string $type, string $description, array $properties = []): Activity
    {
        return Activity::create([
            'user_id' => Auth::id(),
            'ticket_id' => $ticket->id,
            'type' => $type,
            'description' => $description,
            'properties' => $properties,
        ]);
    }

    protected function invalidateCache(Ticket $ticket): void
    {
        Cache::forget("dashboard:{$ticket->created_by}");
        Cache::forget("sidebar:{$ticket->created_by}");

        if ($ticket->assigned_to) {
            Cache::forget("dashboard:{$ticket->assigned_to}");
            Cache::forget("sidebar:{$ticket->assigned_to}");
        }

        foreach (User::role(['Admin', 'Agente'])->pluck('id') as $userId) {
            Cache::forget("dashboard:{$userId}");
            Cache::forget("sidebar:{$userId}");
        }
    }
}
