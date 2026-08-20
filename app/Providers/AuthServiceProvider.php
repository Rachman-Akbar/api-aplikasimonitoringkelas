<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Policies\UserPolicy;
use App\Policies\GuruPolicy;
use App\Policies\SiswaPolicy;
use App\Policies\JadwalPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Guru::class => GuruPolicy::class,
        Siswa::class => SiswaPolicy::class,
        Jadwal::class => JadwalPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define custom gates for role-based access
        Gate::define('admin-only', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('admin-or-kepsek', function ($user) {
            return $user->hasRole('admin') || $user->hasRole('kepsek');
        });

        Gate::define('admin-or-kurikulum', function ($user) {
            return $user->hasRole('admin') || $user->hasRole('kurikulum');
        });

        Gate::define('guru-or-kurikulum', function ($user) {
            return $user->hasRole('guru') || $user->hasRole('kurikulum');
        });

        Gate::define('guru-only', function ($user) {
            return $user->hasRole('guru');
        });
    }
}
