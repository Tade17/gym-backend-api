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
        'profile_photo',
        'assigned_trainer_id'//solo para clientes
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
    public function plans(){
        return $this->hasMany(Plan::class, 'trainer_id');
    }
     
    public function subscription(){
        return $this->hasOne(Subscription::class, 'user_id');
    }
}
