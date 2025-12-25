<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutExerciseLog extends Model
{
    protected $fillable = [
        'workout_log_id',
        'exercise_id',
        'actual_sets',
        'actual_reps',
        'weight_used'
    ];

    public function workoutLog()
    {
        return $this->belongsTo(WorkoutLog::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
