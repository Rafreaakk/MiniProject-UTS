<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\Models\Report;
use App\Observers\ReportObserver;

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
        Report::observe(ReportObserver::class);

        Gate::define(
            'submit-reports',
            fn (User $user): bool =>$user->role === 'pelapor'
        );

        Gate::define(
        'review-reports',
        fn (User $user): bool => $user->role === 'pemantau'
        );

        Gate::define(
            'manage-supporting-data',
            fn (User $user): bool => $user->role === 'koordinator'
        );

        Gate::define(
            'record-forwarding',
            fn (User $user): bool => $user->role === 'koordinator'
        );

        Gate::define(
            'view-recap',
            fn (User $user): bool => in_array(
                $user->role,
                ['pemantau', 'koordinator'],
                true
            )
        );
    }
}
