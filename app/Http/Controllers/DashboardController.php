<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Activity;
use App\Models\Status;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isStaff = $user->hasAnyRole(['Admin', 'Agente']);

        // Stats
        $stats = [
            'total' => Ticket::when(!$isStaff, fn($q) => $q->where('created_by', $user->id))->count(),
            'open' => Ticket::whereHas('status', fn($q) => $q->where('name', 'Abierto'))
                        ->when(!$isStaff, fn($q) => $q->where('created_by', $user->id))
                        ->count(),
            'in_progress' => Ticket::whereHas('status', fn($q) => $q->where('name', 'En Progreso'))
                            ->when(!$isStaff, fn($q) => $q->where('created_by', $user->id))
                            ->count(),
            'resolved' => Ticket::whereHas('status', fn($q) => $q->where('name', 'Cerrado'))
                            ->when(!$isStaff, fn($q) => $q->where('created_by', $user->id))
                            ->count(),
        ];

        // Critical Tickets (High priority and not closed)
        $criticalTickets = Ticket::whereHas('priority', fn($q) => $q->where('level', '>=', 3))
            ->whereHas('status', fn($q) => $q->where('name', '!=', 'Cerrado'))
            ->when(!$isStaff, fn($q) => $q->where('created_by', $user->id))
            ->with(['status', 'priority'])
            ->latest()
            ->take(3)
            ->get();

        // Recent Tickets
        $recentTickets = Ticket::when(!$isStaff, fn($q) => $q->where('created_by', $user->id))
            ->with(['status', 'priority'])
            ->latest()
            ->take(5)
            ->get();

        // Category distribution (for chart)
        $categories = Category::withCount(['tickets' => function($q) use ($isStaff, $user) {
            $q->when(!$isStaff, fn($sq) => $sq->where('created_by', $user->id));
        }])->get();

        // Activity log
        $activities = Activity::with(['user', 'ticket'])
            ->when(!$isStaff, function($q) use ($user) {
                $q->whereHas('ticket', fn($sq) => $sq->where('created_by', $user->id))
                  ->orWhere('user_id', $user->id);
            })
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'criticalTickets', 'recentTickets', 'categories', 'activities'));
    }
}
