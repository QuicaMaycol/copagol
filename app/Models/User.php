<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'country',
        'is_verified',
        'plan_type',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Set the user's name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = ucfirst(strtolower($value));
    }

    /**
     * Set the user's country.
     *
     * @param  string  $value
     * @return void
     */
    public function setCountryAttribute(string $value): void
    {
        $this->attributes['country'] = ucfirst(strtolower($value));
    }

    public function campeonatos()
    {
        return $this->hasMany(Campeonato::class);
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    /**
     * The championships that this user is a delegate of.
     */
    public function delegatedCampeonatos()
    {
        return $this->belongsToMany(Campeonato::class, 'campeonato_user');
    }

    /**
     * Check if the user is a delegate of a given championship.
     *
     * @param  \App\Models\Campeonato  $campeonato
     * @return bool
     */
    public function isDelegateOf(Campeonato $campeonato): bool
    {
        return $this->delegatedCampeonatos->contains($campeonato);
    }

    /**
     * Get the team registered by this user (as a delegate) in a given championship.
     *
     * @param  \App\Models\Campeonato  $campeonato
     * @return \App\Models\Equipo|null
     */
    public function getTeamInCampeonato(Campeonato $campeonato): ?\App\Models\Equipo
    {
        return $this->equipos()->where('campeonato_id', $campeonato->id)->first();
    }
}
