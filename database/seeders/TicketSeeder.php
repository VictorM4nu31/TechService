<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\Comment;
use App\Models\Activity;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        // Create some teams first
        $teams = Team::factory(3)->create();
        
        // Assign team members
        $agents = User::role('Agente')->get();
        foreach ($teams as $team) {
            $memberCount = min(rand(1, 3), $agents->count());
            if ($memberCount > 0) {
                $team->members()->attach($agents->random($memberCount)->pluck('id'));
            }
        }

        // Create tickets with associated activities and comments
        $clients = User::role('Cliente')->get();
        $agentsForAssignment = User::role('Agente')->get();

        Ticket::factory(15)->create()->each(function ($ticket) use ($agentsForAssignment) {
            // Create initial activity
            Activity::create([
                'user_id' => $ticket->created_by,
                'ticket_id' => $ticket->id,
                'type' => 'created',
                'description' => 'creó el ticket',
            ]);

            // If assigned, create assignment activity
            if ($ticket->assigned_to) {
                Activity::create([
                    'user_id' => $agentsForAssignment->random()->id,
                    'ticket_id' => $ticket->id,
                    'type' => 'assigned',
                    'description' => 'asignó el ticket a ' . $ticket->assignee->name,
                ]);
            }

            // Add some comments (50% chance)
            if (rand(0, 1)) {
                Comment::factory(rand(1, 3))->create([
                    'ticket_id' => $ticket->id,
                ])->each(function ($comment) use ($ticket) {
                    Activity::create([
                        'user_id' => $comment->user_id,
                        'ticket_id' => $ticket->id,
                        'type' => 'commented',
                        'description' => 'comentó en el ticket',
                        'properties' => ['comment_id' => $comment->id],
                    ]);
                });
            }
        });
    }
}
