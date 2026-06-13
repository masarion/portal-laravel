<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
    {
        $projects = Project::withCount(['staffMembers', 'submissions' => fn($q) => $q->where('submitted', true)])
            ->orderByDesc('deadline')
            ->get();
        return view('shift.management.dashboard', compact('projects'));
    }
}
