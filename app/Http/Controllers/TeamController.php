<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Team::class);

        $teams = Team::with(['leader', 'members'])->get();

        return view('teams.index', compact('teams'));
    }
}
