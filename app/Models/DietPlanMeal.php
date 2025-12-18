<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DietPlanMeal extends Model
{
    protected $table = 'diet_plan_meal';

    protected $fillable = [
        'diet_plan_id',
        'meal_id',
        'suggested_time',
        'meal_type',
        'day_of_week'
    ];
}
