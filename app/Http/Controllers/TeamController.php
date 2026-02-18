<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with(['leader', 'members'])->get();
        return view('teams.index', compact('teams'));
    }
}
