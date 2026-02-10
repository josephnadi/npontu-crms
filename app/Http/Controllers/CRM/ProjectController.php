<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Client;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::with(['owner', 'client', 'deal'])
            ->where('owner_id', Auth::id());

        // Apply filters
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        $projects = $query->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        // Calculate stats
        $stats = [
            'total' => Project::where('owner_id', Auth::id())->count(),
            'active' => Project::where('owner_id', Auth::id())->active()->count(),
            'overdue' => Project::where('owner_id', Auth::id())->overdue()->count(),
            'completed' => Project::where('owner_id', Auth::id())->completed()->count(),
        ];

        return Inertia::render('CRM/Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only(['search', 'status', 'priority']),
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CRM/Projects/Create', [
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'deals' => Deal::where('status', 'won')->orderBy('title')->get(['id', 'title']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'client_id' => 'nullable|exists:clients,id',
            'deal_id' => 'nullable|exists:deals,id',
        ]);

        $validated['owner_id'] = Auth::id();

        Project::create($validated);

        return redirect()->route('crm.projects.index')->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorizeOwner($project);
        $project->load(['owner', 'client', 'deal', 'tasks' => function($q) {
            $q->orderBy('due_date', 'asc');
        }, 'activities' => function($q) {
            $q->orderBy('activity_date', 'desc')->limit(10);
        }]);

        // Get financial data
        $invoices = \App\Models\Invoice::where('project_id', $project->id)
            ->orderBy('issue_date', 'desc')
            ->get();

        $financials = [
            'total_invoiced' => $invoices->sum('total_amount'),
            'paid_amount' => $invoices->where('status', 'paid')->sum('total_amount'),
            'pending_amount' => $invoices->where('status', 'pending')->sum('total_amount'),
            'overdue_amount' => $invoices->where('status', 'overdue')->sum('total_amount'),
            'budget_usage_percent' => $project->budget > 0 ? round(($invoices->sum('total_amount') / $project->budget) * 100) : 0,
        ];

        return Inertia::render('CRM/Projects/Show', [
            'project' => $project,
            'invoices' => $invoices,
            'financials' => $financials,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $this->authorizeOwner($project);

        return Inertia::render('CRM/Projects/Edit', [
            'project' => $project,
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'deals' => Deal::where('status', 'won')->orderBy('title')->get(['id', 'title']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorizeOwner($project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'client_id' => 'nullable|exists:clients,id',
            'deal_id' => 'nullable|exists:deals,id',
        ]);

        $project->update($validated);

        return redirect()->route('crm.projects.index')->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorizeOwner($project);
        $project->delete();

        return redirect()->route('crm.projects.index')->with('success', 'Project deleted successfully.');
    }

    protected function authorizeOwner(Project $project)
    {
        if ($project->owner_id !== Auth::id()) {
            abort(403);
        }
    }
}
