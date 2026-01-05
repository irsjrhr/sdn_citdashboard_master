<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME', 300)); // seconds
        Paginator::useBootstrapFive();
        View::composer('layouts.navbar', function ($view) {
            if (auth()->check()) {

                $user = auth()->user();
                $unread = $user->unreadNotifications;

                $view->with([
                    'unreadNotifCount' => $unread->count(),
                    'unreadNotifs'     => $unread
                        ->sortByDesc('created_at')
                        ->take(5)
                ]);
            }
        });
    }
}
