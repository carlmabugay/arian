<?php

namespace App\Providers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;
use App\Observers\AssetAssignmentObserver;
use App\Observers\AssetObserver;
use App\Observers\CompanyObserver;
use App\Observers\LocationObserver;
use App\Observers\UserObserver;
use App\Policies\CompanyPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Company::class, CompanyPolicy::class);
        AssetAssignment::observe(AssetAssignmentObserver::class);
        Company::observe(CompanyObserver::class);
        Location::observe(LocationObserver::class);
        User::observe(UserObserver::class);
        Asset::observe(AssetObserver::class);
    }
}
