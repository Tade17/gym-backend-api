<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class DietPlan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'goal',
        'trainer_id'
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    // Relación Muchos a Muchos con Comidas usando la tabla intermedia
    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'diet_plan_meal')
            ->using(DietPlanMeal::class)
            ->withPivot('id', 'suggested_time', 'meal_type', 'day_of_week')
            ->withTimestamps();
    }
    // app/Models/DietPlan.php
    public function dietPlanMeals()
    {
        return $this->hasMany(DietPlanMeal::class);
    }

    public function assignedDiets()
    {
        return $this->hasMany(AssignedDiet::class);
    }
}
