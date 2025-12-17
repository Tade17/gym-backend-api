<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Importante para la API más adelante

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */


    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'weight',
        'height',
        'goals',
        'role',          // 'admin', 'trainer', 'client'
        'birth_date',
        'profile_photo'
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
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Esto encripta la contraseña automático
    ];
    // ==========================================
    // RELACIONES (Pega esto antes del último "}")
    // ==========================================

    // 1. Relación con Suscripciones
    // Permite hacer: $user->subscriptions (Ver historial de pagos/planes)
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // 2. Relación con Rutinas Asignadas
    // Permite hacer: $user->assignedRoutines (Ver qué le toca entrenar)
    public function assignedRoutines()
    {
        return $this->hasMany(AssignedRoutine::class);
    }

    // 3. Relación con Historial de Entrenamiento
    // Permite hacer: $user->workoutLogs (Ver su progreso en el gym)
    public function workoutLogs()
    {
        return $this->hasMany(WorkoutLog::class);
    }

    // 4. Relación con Dietas Asignadas
    // Permite hacer: $user->assignedDiets (Ver qué debe comer)
    public function assignedDiets()
    {
        return $this->hasMany(AssignedDiet::class);
    }
}
