<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = Task::with(['owner', 'assignee', 'project'])
            ->where('owner_id', Auth::id())
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('CRM/Tasks/Index', [
            'tasks' => $tasks,
            'filters' => $request->only(['search', 'status', 'priority']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Inertia::render('CRM/Tasks/Create', [
            'projects' => Project::where('owner_id', Auth::id())->orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
            'selected_project_id' => $request->project_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['owner_id'] = Auth::id();

        Task::create($validated);

        return redirect()->route('crm.tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorizeOwner($task);
        $task->load(['owner', 'assignee', 'project']);

        return Inertia::render('CRM/Tasks/Show', [
            'task' => $task,
            'suggestions' => app(\App\Services\CRM\TaskIntelligenceService::class)->suggestNextActions($task),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorizeOwner($task);

        return Inertia::render('CRM/Tasks/Edit', [
            'task' => $task,
            'projects' => Project::where('owner_id', Auth::id())->orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorizeOwner($task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update($validated);

        return redirect()->route('crm.tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorizeOwner($task);
        $task->delete();

        return redirect()->route('crm.tasks.index')->with('success', 'Task deleted successfully.');
    }

    protected function authorizeOwner(Task $task)
    {
        if ($task->owner_id !== Auth::id()) {
            abort(403);
        }
    }
}
