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
        'assigned_date',
        'status', // 0=pendiente, 1=completado, 2=omitido
        'rating'
    ];

    // 1. Saber qué Rutina es (para mostrar el nombre "Pierna Destructora")
    public function routine()
    {
        return $this->belongsTo(Routine::class);
    }

    // 2. Saber a qué Usuario pertenece
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}