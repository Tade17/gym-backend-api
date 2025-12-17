<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class AssignedDiet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'diet_plan_id',
        'start_date',
        'end_date'
    ];

    // Relación: ¿A quién se la asignaron?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: ¿Qué dieta es?
    public function dietPlan()
    {
        return $this->belongsTo(DietPlan::class);
    }
}
