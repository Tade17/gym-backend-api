<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Importante para la API más adelante
use Illuminate\Database\Eloquent\Casts\Attribute; 
use Illuminate\Support\Facades\Storage;

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

    // 1. Busca la variable $appends. Si no existe, créala.
    // Esto le dice a Laravel: "Siempre que envíes un usuario, agrega este campo extra".
    protected $appends = [
        'profile_photo_url',
    ];
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            // Si es una URL externa (por si usas Google Login luego), devuélvela tal cual
            if (str_starts_with($this->profile_photo, 'http')) {
                return $this->profile_photo;
            }
            
            // Si es un archivo local, crea el link completo http://127.0.0.1:8000/storage/...
            // NOTA: 'default.png' está en la raíz de public, no en storage, así que validamos:
            if ($this->profile_photo === 'default.png') {
                 return asset('default.png');
            }

            return asset('storage/' . $this->profile_photo);
        }

        // Si no tiene foto, devuelve null o una imagen por defecto
        return asset('default.png'); 
    }


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

    protected static function booted()
{
    static::created(function ($user) {
        if ($user->role === 'trainer') {
            
            $duraciones = [
                ['days' => 30,  'label' => 'Mensual',    'multiplier' => 1],
                ['days' => 90,  'label' => 'Trimestral', 'multiplier' => 3],
                ['days' => 365, 'label' => 'Anual',      'multiplier' => 12],
            ];

            $tipos = [
                ['type' => 'basic',        'name' => 'Plan Básico',        'base_price' => 50],
                ['type' => 'Pro',          'name' => 'Plan Pro',           'base_price' => 100],
                ['type' => 'Personalized', 'name' => 'Plan Personalizado', 'base_price' => 150],
            ];

            foreach ($tipos as $tipo) {
                foreach ($duraciones as $duracion) {
                    
                    // Calculamos un precio base sugerido (opcional, puedes poner 0)
                    $price = $tipo['base_price'] * $duracion['multiplier'];
                    if ($duracion['days'] > 30) {
                        $price = $price * 0.90;
                    }

                    Plan::create([
                    'name'          => $tipo['name'],
                    'type'          => $tipo['type'],
                    'price'         => round($price, 2),
                    'duration_days' => $duracion['days'],
                    
                    // CAMBIO 1: Descripción genérica para obligar a editar
                    'description'   => 'Describe los beneficios de este plan aquí...', 
                    
                    // CAMBIO 2: Todos nacen apagados (false / 0)
                    'is_active'     => false, 
                    
                    'trainer_id'    => $user->id
                ]);
                }
            }
        }
    });
}    
}
