<?php

namespace App\Http\Controllers\CRM;

use App\Models\Engagement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class EngagementController extends Controller
{
    public function index(Request $request)
    {
        $engagements = Engagement::with(['engageable', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->type && $request->type !== 'all', function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->date_range, function ($query, $range) {
                $dates = explode(',', $range);
                if (count($dates) === 2) {
                    $query->whereBetween('engagement_date', [$dates[0], $dates[1]]);
                }
            })
            ->orderBy($request->sort_by ?? 'engagement_date', $request->sort_order ?? 'desc')
            ->paginate($request->per_page ?? 10)
            ->withQueryString();

        return Inertia::render('CRM/Engagement/Index', [
            'engagements' => $engagements,
            'filters' => $request->only(['search', 'type', 'date_range', 'sort_by', 'sort_order']),
            'stats' => [
                'total' => Engagement::count(),
                'this_month' => Engagement::whereMonth('engagement_date', now()->month)->count(),
                'avg_score' => round(Engagement::avg('score') ?? 0, 1),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'score' => 'nullable|integer|min:0|max:100',
            'engagement_date' => 'required|date',
            'engageable_id' => 'required|integer',
            'engageable_type' => 'required|string',
            'metadata' => 'nullable|array',
        ]);

        $engagement = Engagement::create([
            ...$validated,
            'user_id' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        Log::info('Engagement created', ['engagement_id' => $engagement->id, 'user_id' => auth()->id()]);
        
        return redirect()->back()->with('success', 'Engagement logged successfully');
    }

    public function update(Request $request, Engagement $engagement)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'score' => 'nullable|integer|min:0|max:100',
            'engagement_date' => 'required|date',
            'metadata' => 'nullable|array',
        ]);

        $engagement->update([
            ...$validated,
            'updated_by' => auth()->id(),
        ]);

        Log::info('Engagement updated', ['engagement_id' => $engagement->id, 'user_id' => auth()->id()]);

        return redirect()->back()->with('success', 'Engagement updated successfully');
    }

    public function destroy(Engagement $engagement)
    {
        $engagement->delete();
        
        Log::info('Engagement deleted', ['engagement_id' => $engagement->id, 'user_id' => auth()->id()]);
        
        return redirect()->back()->with('success', 'Engagement deleted successfully');
    }
}
