<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealLog extends Model
{
    protected $fillable = [
        'photo_url', // Evidencia visual (RF-20)
        'notes',     // Comentarios del alumno
        'meal_id',   // Qué comida del plan está registrando
        'user_id'    // Quién lo registra
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
}
