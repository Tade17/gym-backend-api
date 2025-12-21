<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedDiet extends Model
{
    protected $fillable = [
        'user_id',
        'diet_plan_id',
        'trainer_id',
        'start_date',
        'end_date'
    ];
    protected $dates = ['start_date', 'end_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dietPlan()
    {
        return $this->belongsTo(DietPlan::class);
    }
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
