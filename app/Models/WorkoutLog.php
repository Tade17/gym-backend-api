<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutLog extends Model
{
    protected $fillable = [
        'assigned_routine_id',
        'user_id',
        'workout_date',
        'duration',
        'weight_used',
        'reps',
        'notes',
    ];

    protected $casts = [
        'workout_date' => 'date',
    ];

    public function assignedRoutine()
    {
        return $this->belongsTo(AssignedRoutine::class);
    }

   
    //Acceso indirecto al usuario (muy útil)
    /*public function user()
    {
        return $this->hasOneThrough(
            User::class,
            AssignedRoutine::class,
            'id',       // FK en assigned_routines
            'id',       // PK en users
            'assigned_routine_id',
            'user_id'
        );
    }*/
}
