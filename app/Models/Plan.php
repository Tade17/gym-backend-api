<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'price',
        'duration_days',
        'description',
        'is_active',
        'trainer_id'
    ];
    //Cada plan pertenece a un entrenador
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
