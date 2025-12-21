<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealLog extends Model
{
    protected $fillable = [
        'user_id',
        'diet_plan_meal_id',
        'assigned_diet_id',
        'consumed_date',
        'photo_url',
        'is_completed'
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
