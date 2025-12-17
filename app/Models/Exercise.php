<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    
    // Lista blanca de campos permitidos
    protected $fillable = [
        'name',
        'description',
        'muscle_group',
        'video_url'
    ];
    // Un ejercicio puede estar en muchas rutinas diferentes
    public function routines() {
    return $this->belongsToMany(Routine::class, 'routine_exercises')
                ->withPivot('sets', 'reps', 'duration_seconds', 'rest_seconds')
                ->withTimestamps();
}
}
