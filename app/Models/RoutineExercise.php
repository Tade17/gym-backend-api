<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RoutineExercise extends Pivot
{
    //
    protected $table = 'routine_exercises';

    protected $fillable = [
        'routine_id',
        'exercise_id',
        'sets',
        'reps',
        'rest_time'
    ];
}
