<?php

namespace App\Policies;

use App\Models\Jugador;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JugadorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Jugador  $jugador
     * @return bool
     */
    public function view(User $user, Jugador $jugador)
    {
        // Allow if the user is the owner of the championship
        if ($user->id === $jugador->equipo->campeonato->user_id) {
            return true;
        }

        // Allow if the user is the owner of the team (the delegate)
        if ($user->id === $jugador->equipo->user_id) {
            return true;
        }

        return false;
    }
}
