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

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Una suscripción pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Una suscripción pertenece a un plan (RF-01)
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
