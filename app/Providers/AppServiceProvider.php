<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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
