<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'level', //beginner , intermadiate,advanced
        'estimated_duration',
        'trainer_id',
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'routine_exercises')
            ->using(RoutineExercise::class) //indica que use LA TABLA INTERMEDIA ENTRE AMBOS MODELOS
            ->withPivot('sets', 'reps', 'rest_time')
            ->withTimestamps();
    }
}
