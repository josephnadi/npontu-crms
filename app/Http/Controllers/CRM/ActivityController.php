<?php

namespace App\Http\Controllers\CRM;

use App\Models\Activity;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $activities = Activity::with('activityable')
            ->when($request->search, function ($query, $search) {
                $query->where('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                if ($type !== 'all') {
                    $query->where('type', $type);
                }
            })
            ->when($request->status, function ($query, $status) {
                if ($status !== 'all') {
                    $query->where('status', $status);
                }
            })
            ->orderBy('activity_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('CRM/Activities/Index', [
            'activities' => $activities,
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    public function calendar()
    {
        return Inertia::render('CRM/Activities/Calendar', [
            'activities' => Activity::whereIn('type', ['meeting', 'call'])
                ->with('activityable')
                ->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('CRM/Activities/Create', [
            'deals' => \App\Models\Deal::orderBy('title')->get(['id', 'title']),
            'leads' => \App\Models\Lead::orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'contacts' => \App\Models\Contact::orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'clients' => \App\Models\Client::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:call,meeting,email,task,note',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_date' => 'required|date',
            'due_date' => 'nullable|date',
            'status' => 'required|string|in:pending,completed,cancelled',
            'activityable_type' => 'required|string',
            'activityable_id' => 'required|integer',
        ]);

        Activity::create([
            ...$validated,
            'owner_id' => auth()->id(),
        ]);

        Log::info('Activity created', ['owner_id' => auth()->id(), 'type' => $validated['type'] ?? null]);
        return redirect()->back()->with('success', 'Activity created successfully');
    }

    public function update(Request $request, Activity $activity)
    {
        if ($activity->owner_id !== auth()->id()) {
            abort(403);
        }
        $validated = $request->validate([
            'type' => 'required|string|in:call,meeting,email,task,note',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_date' => 'required|date',
            'due_date' => 'nullable|date',
            'status' => 'required|string|in:pending,completed,cancelled',
        ]);

        $activity->update($validated);

        if ($request->status === 'completed' && !$activity->completed_at) {
            $activity->update(['completed_at' => now()]);
        }

        Log::info('Activity updated', ['activity_id' => $activity->id, 'user_id' => auth()->id()]);
        return redirect()->back()->with('success', 'Activity updated successfully');
    }

    public function destroy(Activity $activity)
    {
        if ($activity->owner_id !== auth()->id()) {
            abort(403);
        }
        $activity->delete();
        Log::info('Activity deleted', ['activity_id' => $activity->id, 'user_id' => auth()->id()]);
        return redirect()->back()->with('success', 'Activity deleted successfully');
    }
}
