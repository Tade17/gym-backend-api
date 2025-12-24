<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealLog extends Model
{
    protected $fillable = [
        'consumed_date',
        'is_completed',
        'photo_url',
        'notes',
        'assigned_diet_id',
        'diet_plan_meal_id',
        'user_id',
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
