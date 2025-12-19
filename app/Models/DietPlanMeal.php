<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DietPlanMeal extends Pivot
{
    protected $table = 'diet_plan_meal';
    protected $fillable = [
        'suggested_time',
        'meal_type',
        'day_of_week'
    ];

    public function dietPlan()
    {
        return $this->belongsTo(DietPlan::class);
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
}
