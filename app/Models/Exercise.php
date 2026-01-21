<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'muscle_group',
        'video_url'
    ];

    //un ejercicio puede estar en muchas rutinas
    public function routines()
    {
        return $this->belongsToMany(Routine::class, 'routine_exercises')
            ->using(RoutineExercise::class)//indica que use LA TABLA INTERMEDIA ENTRE AMBOS MODELOS
            ->withPivot('sets', 'reps', 'rest_time', 'notes')
            ->withTimestamps();
    }
}
