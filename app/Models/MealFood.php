<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MealFood extends Pivot
{
    protected $table = "meal_food";

    protected $fillable = [
        'meal_id',
        'food_id',
        'quantity'
    ];
}
