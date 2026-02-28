<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Activity;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class TicketService
{
    public function createTicket(array $data)
    {
        $ticket = Ticket::create($data);

        $this->logActivity($ticket, 'created', 'creó el ticket');

        return $ticket;
    }

    public function assignTicket(Ticket $ticket, $userId)
    {
        $ticket->update(['assigned_to' => $userId]);
        
        $user = \App\Models\User::find($userId);
        $this->logActivity($ticket, 'assigned', "asignó el ticket a {$user->name}", ['assigned_to' => $userId]);
    }

    public function addComment(Ticket $ticket, $content, $attachments = [])
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

    public function updateStatus(Ticket $ticket, $statusId)
    {
        $oldStatus = $ticket->status->name;
        $ticket->update(['status_id' => $statusId]);
        $ticket->load('status');
        $newStatus = $ticket->status->name;

        $this->logActivity($ticket, 'status_updated', "cambió el estado de {$oldStatus} a {$newStatus}", [
            'old_status_id' => $ticket->getOriginal('status_id'),
            'new_status_id' => $statusId
        ]);
    }

    public function resolveTicket(Ticket $ticket)
    {
        $resolvedStatus = \App\Models\Status::where('name', 'Cerrado')->first();

        if (!$resolvedStatus) {
            throw new \RuntimeException('El estado "Cerrado" no existe en la base de datos. Por favor ejecute los seeders.');
        }

        $this->updateStatus($ticket, $resolvedStatus->id);
        $this->logActivity($ticket, 'resolved', 'resolvió el ticket');
    }

    protected function logActivity(Ticket $ticket, string $type, string $description, array $properties = [])
    {
        Activity::create([
            'user_id' => Auth::id(),
            'ticket_id' => $ticket->id,
            'type' => $type,
            'description' => $description,
            'properties' => $properties,
        ]);
    }
}
