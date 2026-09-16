<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Activity;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $cacheKey = "dashboard:{$user->id}";

        $data = cache()->remember($cacheKey, 60, function () use ($user) {
            $stats = [
                'total' => Ticket::visibleTo($user)->count(),
                'open' => Ticket::visibleTo($user)->where('status', TicketStatus::Abierto)->count(),
                'in_progress' => Ticket::visibleTo($user)->where('status', TicketStatus::EnProgreso)->count(),
                'resolved' => Ticket::visibleTo($user)->where('status', TicketStatus::Cerrado)->count(),
            ];

            $criticalTickets = Ticket::visibleTo($user)
                ->where('priority', TicketPriority::Alta)
                ->where('status', '!=', TicketStatus::Cerrado)
                ->latest()->take(3)->get();

            $recentTickets = Ticket::visibleTo($user)
                ->whereNotIn('id', $criticalTickets->pluck('id'))
                ->latest()->take(5)->get();

            $categories = Ticket::visibleTo($user)
                ->selectRaw('category, count(*) as tickets_count')
                ->groupBy('category')
                ->get();

            $activities = Activity::with(['user', 'ticket'])
                ->when(! $user->hasAnyRole(['Admin', 'Agente']), fn ($q) => $q
                    ->whereHas('ticket', fn ($sq) => $sq->where('created_by', $user->id))
                    ->orWhere('user_id', $user->id))
                ->latest()->take(10)->get();

            return compact('stats', 'criticalTickets', 'recentTickets', 'categories', 'activities');
        });

        return view('dashboard', $data);
    }
}
