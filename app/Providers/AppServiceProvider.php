<?php

namespace App\Providers;

use App\Models\Lead;
use App\Models\Task;
use App\Models\Engagement;
use App\Observers\LeadObserver;
use App\Observers\TaskObserver;
use App\Observers\EngagementObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Lead::observe(LeadObserver::class);
        Task::observe(TaskObserver::class);
        Engagement::observe(EngagementObserver::class);
        
        Inertia::share([
            'auth' => function () {
                $user = Auth::user();
                if (!$user) return null;
                return [
                    'user' => $user->only(['id', 'name', 'email', 'avatar']),
                ];
            },
            'notifications' => function () {
                $user = Auth::user();
                if (!$user) return ['count' => 0, 'latest' => []];
                return [
                    'count' => $user->unreadNotifications()->count(),
                    'latest' => $user->unreadNotifications()->take(5)->get()->map(function ($n) {
                        return [
                            'id' => $n->id,
                            'data' => $n->data,
                            'created_at' => $n->created_at,
                        ];
                    }),
                ];
            },
        ]);
    }
}
