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
        $isStaff = $user->hasAnyRole(['Admin', 'Agente']);
        $cacheKey = "dashboard:{$user->id}";

        $data = cache()->remember($cacheKey, 60, function () use ($user, $isStaff) {
            $stats = [
                'total' => Ticket::when(! $isStaff, fn ($q) => $q->where('created_by', $user->id))->count(),
                'open' => Ticket::where('status', TicketStatus::Abierto)
                    ->when(! $isStaff, fn ($q) => $q->where('created_by', $user->id))->count(),
                'in_progress' => Ticket::where('status', TicketStatus::EnProgreso)
                    ->when(! $isStaff, fn ($q) => $q->where('created_by', $user->id))->count(),
                'resolved' => Ticket::where('status', TicketStatus::Cerrado)
                    ->when(! $isStaff, fn ($q) => $q->where('created_by', $user->id))->count(),
            ];

            $criticalTickets = Ticket::where('priority', TicketPriority::Alta)
                ->where('status', '!=', TicketStatus::Cerrado)
                ->when(! $isStaff, fn ($q) => $q->where('created_by', $user->id))
                ->latest()->take(3)->get();

            $recentTickets = Ticket::when(! $isStaff, fn ($q) => $q->where('created_by', $user->id))
                ->latest()->take(5)->get();

            $categories = Ticket::selectRaw('category, count(*) as tickets_count')
                ->when(! $isStaff, fn ($q) => $q->where('created_by', $user->id))
                ->groupBy('category')
                ->get();

            $activities = Activity::with(['user', 'ticket'])
                ->when(! $isStaff, fn ($q) => $q
                    ->whereHas('ticket', fn ($sq) => $sq->where('created_by', $user->id))
                    ->orWhere('user_id', $user->id))
                ->latest()->take(10)->get();

            return compact('stats', 'criticalTickets', 'recentTickets', 'categories', 'activities');
        });

        return view('dashboard', $data);
    }
}
