<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedRoutine extends Model
{
    use HasFactory;


    protected $fillable = [
        'assigned_date',
        'status', // 0=pendiente, 1=completado, 2=omitido
        'rating',
        'routine_id',
        'user_id',
        'trainer_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function routine()
    {
        return $this->belongsTo(Routine::class);
    }

    public function logs()
    {
        return $this->hasMany(WorkoutLog::class);
    }
}
