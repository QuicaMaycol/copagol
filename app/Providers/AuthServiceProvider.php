<?php

namespace App\Providers;

use App\Models\Campeonato;
use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\User;
use App\Policies\CampeonatoPolicy;
use App\Policies\EquipoPolicy;
use App\Policies\JugadorPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Campeonato::class => CampeonatoPolicy::class,
        Equipo::class => EquipoPolicy::class,
        User::class => UserPolicy::class,
        Jugador::class => JugadorPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Grant all permissions to the admin user
        Gate::before(function ($user, $ability) {
            if ($user->email === 'admin@copago.com.pe') {
                return true;
            }
        });
    }
}
