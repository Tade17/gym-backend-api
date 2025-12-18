<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'suggested_time',
        'diet_plan_id'
    ];

    public function dietPlan()
    {
        return $this->belongsTo(DietPlan::class);
    }
}
