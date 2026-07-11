<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\SubmissionRepositoryInterface::class,
            \App\Repositories\SubmissionRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure every activity has a `module` property when not explicitly provided
        Activity::creating(function (Activity $activity) {
            $props = $activity->properties ?? collect();

            if (is_array($props)) {
                $props = collect($props);
            }

            if ($props->has('module')) {
                $activity->properties = $props;
                return;
            }

            $module = null;

            // Prefer explicit subject type mapping for friendly module names
            $map = [
                \App\Models\User::class => 'Users',
                \App\Models\Submission::class => 'Submissions',
                \App\Models\Approval::class => 'Approvals',
                \App\Models\Category::class => 'Categories',
                \App\Models\Budget::class => 'Budgets',
                \App\Models\Payment::class => 'Payments',
            ];

            if (! empty($activity->subject_type) && isset($map[$activity->subject_type])) {
                $module = $map[$activity->subject_type];
            } elseif (! empty($activity->subject_type)) {
                $module = class_basename($activity->subject_type);
            } elseif (! empty($activity->log_name)) {
                $module = ucfirst($activity->log_name);
            } else {
                $module = 'General';
            }

            $activity->properties = $props->put('module', $module);
        });
    }
}
