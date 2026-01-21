<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{

    use HasFactory;
    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'status' //active,expired
    ];

    // Una suscripción pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Una suscripción pertenece a un plan (RF-01)
    public function plan()
    {
        // Esto le dice a Laravel que la suscripción "pertenece a" un Plan
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
