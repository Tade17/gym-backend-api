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
        'status' //active,expired,cancelled
    ];

    //relaciones
    //Esta funcion define la relacion entre Subscription y User
    //Una suscripción pertenece a un Usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Esta funcion define la relacion entre Subscription y Plan
    //Una suscripción pertenece a un Plan
    public function plan()
    {
        return $this->belongTo(Plan::class);
    }
}
