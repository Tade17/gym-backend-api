<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutLog extends Model
{
    protected $fillable = [
        'assigned_routine_id',
        'workout_date',
        'duration',
        'weight_used',
        'reps',
        'notes'
    ];

    public function assignedRoutine()
    {
        return $this->belongsTo(AssignedRoutine::class);
    }
}
