<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedDiet extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'diet_plan_id',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dietPlan()
    {
        return $this->belongsTo(DietPlan::class);
    }
}
