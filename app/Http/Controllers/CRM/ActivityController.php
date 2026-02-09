<?php

namespace App\Http\Controllers\CRM;

use App\Models\Activity;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index()
    {
        return Inertia::render('CRM/Activities/Index', [
            'activities' => Activity::with('activityable')->orderBy('activity_date', 'desc')->get(),
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
            'deals' => \App\Models\Deal::all(['id', 'title']),
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

        Activity::create($validated);

        return redirect()->back()->with('success', 'Activity created successfully');
    }

    public function update(Request $request, Activity $activity)
    {
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

        return redirect()->back()->with('success', 'Activity updated successfully');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->back()->with('success', 'Activity deleted successfully');
    }
}
