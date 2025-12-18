<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;//HasFactory sirve para usar fábricas de modelos en Laravel, lo que facilita la creación de instancias de modelos para pruebas y generación de datos de ejemplo.

    // Lista blanca de campos permitidos
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
            ->withPivot('sets', 'reps', 'rest_time');
    }
}
