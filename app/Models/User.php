<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Importante para la API más adelante

// === AGREGAR ESTOS IMPORTS QUE FALTAN ===
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Routine;
use App\Models\DietPlan;
use App\Models\AssignedRoutine;
use App\Models\AssignedDiet;
use App\Models\WorkoutLog;
use App\Models\MealLog;
// ========================================

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
    'phone_number',     // <--- Asegúrate que este esté
    'gender',
    'password',
    'role',
    'weight',
    'height',
    'birth_date',
    'goals',
    'profile_photo',
    'assigned_trainer_id', // <--- AGREGA ESTO OBLIGATORIAMENTE
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

    //Si soy cliente, tengo un solo entrenador
    public function trainer()
    {
        return $this->belongsTo(User::class, 'assigned_trainer_id');
    }

    // Si soy entrenador, tengo muchos clientes
    public function client()
    {
        return $this->hasMany(User::class, 'assigned_trainer_id');
    }

    // Relación: Un entrenador puede tener muchos planes
    public function plans()
    {
        return $this->hasMany(Plan::class, 'train er_id');
    }
    public function routines()
    {
        return $this->hasMany(Routine::class, 'trainer_id');
    }

    // RELACIÓN QUE FALTA: Un usuario tiene muchas suscripciones
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
    public function dietPlans()
    {
        return $this->hasMany(DietPlan::class, 'trainer_id');
    }

    // RELACIÓN: Un usuario tiene muchas rutinas asignadas
    public function assignedRoutines()
    {
        return $this->hasMany(AssignedRoutine::class);
    }

    // RELACIÓN: Un usuario tiene muchas dietas asignadas
    public function assignedDiets()
    {
        return $this->hasMany(AssignedDiet::class);
    }

    public function workoutLogs()
    {
        return $this->hasManyThrough(
            WorkoutLog::class,
            AssignedRoutine::class,
            'user_id',              // FK en assigned_routines
            'assigned_routine_id',  // FK en workout_logs
            'id',                   // PK en users
            'id'                    // PK en assigned_routines
        );
    }


    public function mealLogs()
    {
        return $this->hasMany(MealLog::class);
    }
}
