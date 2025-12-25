<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MealFood;

class Meal extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    public function dietPlans()
    {
        return $this->belongsToMany(DietPlan::class, 'diet_plan_meal')
            ->using(DietPlanMeal::class)
            ->withPivot('suggested_time', 'meal_type', 'day_of_week')
            ->withTimestamps();
    }
    public function food()
    {
        return $this->belongsToMany(Food::class,'meal_food')
            ->using(MealFood::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
