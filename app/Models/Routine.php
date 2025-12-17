<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    //
    
    protected $fillable =[
        'name',
        'description',
        'level', //beginner , intermadiate,advanced,elite
        'estimated_duration',
        'trainer_id'
    ];
    // Relación con el Entrenador que creó la rutina
    public function trainer() {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    // Relación Muchos a Muchos con Ejercicios
    // IMPORTANTE: 'withPivot' recupera las series, repeticiones y descanso
    public function exercises() {
        return $this->belongsToMany(Exercise::class, 'routine_exercises')
                    ->withPivot('sets', 'reps', 'duration_seconds', 'rest_seconds')
                    ->withTimestamps();
    }
}
