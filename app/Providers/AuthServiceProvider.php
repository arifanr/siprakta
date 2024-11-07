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

        Gate::define('ListUser', function($user){
            return $user->hasRole('superadmin');
        });
    }
}
