<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Lead;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Stat Cards
        $totalContacts = Contact::count();
        $openDealsCount = Deal::where('status', 'open')->count();
        $totalDealValue = Deal::where('status', 'open')->sum('value');
        $activeActivitiesCount = Activity::where('status', 'pending')->count();

        // 2. Intelligence Metrics
        $highPotentialLeads = Lead::where('status', '!=', 'converted')
            ->orderBy('score', 'desc')
            ->take(5)
            ->get();

        $criticalTasks = Task::where('status', '!=', 'completed')
            ->orderBy('priority_score', 'desc')
            ->take(5)
            ->get();

        // 3. Pipeline Funnel (Deals by Stage)
        $pipelineData = DealStage::withCount(['deals' => function ($q) {
                $q->where('status', 'open');
            }])
            ->orderBy('order_column')
            ->get()
            ->map(function ($stage) {
                return [
                    'name' => $stage->name,
                    'count' => $stage->deals_count,
                ];
            });

        // 4. Activity Breakdown
        $activityBreakdown = Activity::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();

        // 5. Recent Activities
        $recentActivities = Activity::with('activityable')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('CRM/Dashboard/Index', [
            'metrics' => [
                'totalContacts' => $totalContacts,
                'openDeals' => $openDealsCount,
                'totalValue' => $totalDealValue,
                'pendingActivities' => $activeActivitiesCount,
                'highPotentialLeadsCount' => Lead::where('score', '>', 70)->count(),
                'slaBreachesCount' => Task::whereNotNull('escalated_at')->where('status', '!=', 'completed')->count(),
            ],
            'highPotentialLeads' => $highPotentialLeads,
            'criticalTasks' => $criticalTasks,
            'pipelineData' => $pipelineData,
            'activityBreakdown' => $activityBreakdown,
            'recentActivities' => $recentActivities,
        ]);
    }
}
