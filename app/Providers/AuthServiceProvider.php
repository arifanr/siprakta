<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('dashboard', function($user){
            return $user->hasAnyRoles([
                'admin', 
                'pembimbing_kp', 
                'pembimbing_ta',
                'koordinator',
                'student',
                'pembimbing_akademik',
            ]);
        });

        Gate::define('ApproveDenyInternship', function($user){
            return $user->hasAnyRoles([
                'admin',
                'koordinator',
            ]);
        });

        Gate::define('CreateInternship', function($user){
            return $user->hasAnyRoles([
                'admin',
                'student',
            ]);
        });

        Gate::define('EditInternship', function($user){
            return $user->hasAnyRoles([
                'admin',
                'student',
                'koordinator',
            ]);
        });

        Gate::define('ApproveDenyFinalProject', function($user){
            return $user->hasAnyRoles([
                'admin',
                'koordinator',
            ]);
        });

        Gate::define('CreateFinalProject', function($user){
            return $user->hasAnyRoles([
                'admin',
                'student',
            ]);
        });

        Gate::define('EditFinalProject', function($user){
            return $user->hasAnyRoles([
                'admin',
                'student',
            ]);
        });

        Gate::define('ApproveDenySeminar', function($user){
            return $user->hasAnyRoles([
                'admin',
                'koordinator',
            ]);
        });

        Gate::define('CreateSeminar', function($user){
            return $user->hasAnyRoles([
                'admin',
                'student',
            ]);
        });

        Gate::define('EditSeminar', function($user){
            return $user->hasAnyRoles([
                'admin',
                'koordinator',
            ]);
        });

        Gate::define('EditSeminarStudent', function($user){
            return $user->hasAnyRoles([
                'student',
            ]);
        });
    }
}
