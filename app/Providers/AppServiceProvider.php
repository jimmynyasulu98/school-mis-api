<?php

namespace App\Providers;

use App\Models\Assessment;
use App\Policies\AssessmentPolicy;
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
        \Illuminate\Support\Facades\Gate::policy(Assessment::class, AssessmentPolicy::class);
    }
}
