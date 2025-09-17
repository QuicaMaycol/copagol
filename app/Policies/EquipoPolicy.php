<?php

namespace App\Policies;

use App\Models\Equipo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EquipoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Equipo  $equipo
     * @return bool
     */
    public function update(User $user, Equipo $equipo)
    {
        // Allow if the user is the owner of the championship (the admin of the tournament)
        if ($user->id === $equipo->campeonato->user_id) {
            return true;
        }

        // Allow if the user is the owner of the team (the delegate)
        if ($user->id === $equipo->user_id) {
            return true;
        }

        return false;
    }
}
