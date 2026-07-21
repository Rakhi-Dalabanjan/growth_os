<?php

namespace App\Providers;

use App\Models\BrandProfile;
use App\Models\Organization;
use App\Models\BrandIntelligence;
use App\Models\MarketingStrategy;
use App\Models\ContentCalendar;
use App\Policies\BrandProfilePolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\BrandIntelligencePolicy;
use App\Policies\MarketingStrategyPolicy;
use App\Policies\ContentCalendarPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // Register Organization Policy
        Gate::policy(Organization::class, OrganizationPolicy::class);

        // Register Brand Profile Policy
        Gate::policy(BrandProfile::class, BrandProfilePolicy::class);

        // Register Brand Intelligence Policy
        Gate::policy(BrandIntelligence::class, BrandIntelligencePolicy::class);

        // Register Marketing Strategy Policy
        Gate::policy(MarketingStrategy::class, MarketingStrategyPolicy::class);

        // Register Content Calendar Policy
        Gate::policy(ContentCalendar::class, ContentCalendarPolicy::class);

        // Set default password validation rules
        Password::defaults(function () {
            return Password::min(8);
        });
    }
}
