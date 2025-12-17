<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietPlan extends Model
{
    use HasFactory;
    

    protected $fillable=[
        'name',
        'description',
        'goal',
        'trainer_id'
    ];
    public function trainer(){
        return $this->belongsTo(User::class,'trainer_id');
    }
}
