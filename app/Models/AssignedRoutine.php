<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedRoutine extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'routine_id',
        'trainer_id',
        'assigned_date',
        'status', // 0=pendiente, 1=completado, 2=omitido
        'rating'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function routine()
    {
        return $this->belongsTo(Routine::class);
    }
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function workoutLogs()
    {
        return $this->hasMany(WorkoutLog::class);
    }
}
