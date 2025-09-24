<?php

namespace App\Policies;

use App\Models\Campeonato;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampeonatoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campeonato  $campeonato
     * @return bool
     */
    public function manageCampeonato(User $user, Campeonato $campeonato)
    {
        return $user->role === 'admin' || $user->id === $campeonato->user_id;
    }

    /**
     * Determine whether the user can share the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campeonato  $campeonato
     * @return bool
     */
    public function share(User $user, Campeonato $campeonato)
    {
        // Allow admin, the championship organizer, or any registered delegate
        return $user->role === 'admin' || $user->id === $campeonato->user_id || $campeonato->delegates->contains($user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campeonato  $campeonato
     * @return bool
     */
    public function update(User $user, Campeonato $campeonato)
    {
        return $user->role === 'admin' || $user->id === $campeonato->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campeonato  $campeonato
     * @return bool
     */
    public function delete(User $user, Campeonato $campeonato)
    {
        return $user->role === 'admin' || $user->id === $campeonato->user_id;
    }
}
