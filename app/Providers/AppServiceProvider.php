<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\InboxService;
use App\Services\DashboardService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
                
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $user = auth()->user();

            if (!$user) {
                $view->with('inboxUnreadCount', 0);
                return;
            }

            $role = $user->role;

            $unreadCount = app(InboxService::class)->getUnreadCount($role);

            $view->with([
                'inboxUnreadCount' => $unreadCount,
            ]);

        });

        require_once app_path('Helpers/helper.php');
    }
}
