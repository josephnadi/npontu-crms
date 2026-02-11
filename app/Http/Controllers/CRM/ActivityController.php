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
        $activities = Activity::with(['activityable', 'owner'])
            ->where('owner_id', auth()->id())
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->type && $request->type !== 'all', function ($query) use ($request) {
                $query->byType($request->type);
            })
            ->when($request->status && $request->status !== 'all', function ($query) use ($request) {
                if ($request->status === 'overdue') {
                    $query->overdue();
                } else {
                    $query->where('status', $request->status);
                }
            })
            ->when($request->date_range, function ($query, $range) {
                $dates = explode(',', $range);
                if (count($dates) === 2) {
                    $query->whereBetween('activity_date', [$dates[0], $dates[1]]);
                }
            })
            ->orderBy($request->sort_by ?? 'activity_date', $request->sort_order ?? 'desc')
            ->paginate($request->per_page ?? 10)
            ->withQueryString();

        return Inertia::render('CRM/Activities/Index', [
            'activities' => $activities,
            'filters' => $request->only(['search', 'type', 'status', 'date_range', 'sort_by', 'sort_order']),
            'stats' => [
                'pending' => Activity::where('owner_id', auth()->id())->pending()->count(),
                'overdue' => Activity::where('owner_id', auth()->id())->overdue()->count(),
                'completed_today' => Activity::where('owner_id', auth()->id())
                    ->whereDate('completed_at', now()->toDateString())
                    ->count(),
            ]
        ]);
    }

    public function complete(Activity $activity)
    {
        if ($activity->owner_id !== auth()->id()) {
            abort(403);
        }

        $activity->markAsCompleted();

        Log::info('Activity marked as completed', ['activity_id' => $activity->id, 'user_id' => auth()->id()]);
        return redirect()->back()->with('success', 'Activity completed successfully');
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:activities,id',
            'status' => 'required|string|in:pending,completed,cancelled',
        ]);

        Activity::whereIn('id', $validated['ids'])
            ->where('owner_id', auth()->id())
            ->update([
                'status' => $validated['status'],
                'completed_at' => $validated['status'] === 'completed' ? now() : null,
            ]);

        return redirect()->back()->with('success', 'Activities updated successfully');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:activities,id',
        ]);

        Activity::whereIn('id', $validated['ids'])
            ->where('owner_id', auth()->id())
            ->delete();

        return redirect()->back()->with('success', 'Activities deleted successfully');
    }

    public function calendar()
    {
        return Inertia::render('CRM/Activities/Calendar', [
            'activities' => Activity::whereIn('type', ['meeting', 'call'])
                ->with('activityable')
                ->get(),
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('CRM/Activities/Create', [
            'deals' => \App\Models\Deal::orderBy('title')->get(['id', 'title']),
            'leads' => \App\Models\Lead::orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'contacts' => \App\Models\Contact::orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'clients' => \App\Models\Client::orderBy('name')->get(['id', 'name']),
            'projects' => \App\Models\Project::orderBy('name')->get(['id', 'name']),
            'initial_activityable_type' => $request->activityable_type,
            'initial_activityable_id' => $request->activityable_id,
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
