<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'price',
        'duration_days',
        'is_active',
        'trainer_id'
    ];
    //Cada plan pertenece a un entrenador
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
    // Un plan puede tener muchas suscripciones (ventas)
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
