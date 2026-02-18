<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Status;
use App\Models\Priority;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::query()
            ->when($request->status, fn($q) => $q->whereHas('status', fn($sq) => $sq->where('name', $request->status)))
            ->when($request->category, fn($q) => $q->whereHas('category', fn($cq) => $cq->where('name', $request->category)))
            ->when(!auth()->user()->hasAnyRole(['Admin', 'Agente']), fn($q) => $q->where('created_by', auth()->id()))
            ->with(['status', 'priority', 'category', 'creator', 'assignee'])
            ->latest()
            ->get();

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        
        $ticket->load(['status', 'priority', 'category', 'creator', 'assignee', 'comments.user', 'comments.media']);
        
        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        return view('tickets.edit', compact('ticket'));
    }
}
