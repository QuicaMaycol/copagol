<?php

namespace App\Providers;

use App\Models\Campeonato;
use App\Models\Equipo;
use App\Models\User;
use App\Policies\CampeonatoPolicy;
use App\Policies\EquipoPolicy;
use App\Policies\UserPolicy; // Added
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Equipo::class => EquipoPolicy::class,
        Campeonato::class => CampeonatoPolicy::class,
        User::class => UserPolicy::class, // Added
    ];

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
        Gate::before(function ($user, $ability) {
            if ($user->role === 'admin') {
                return true;
            }
        });

        Gate::define('manage-campeonato', function (User $user, Campeonato $campeonato) {
            return $user->role === 'admin' || $user->id === $campeonato->user_id;
        });
    }
}
