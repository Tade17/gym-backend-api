<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    //
    
    protected $fillable =[
        'name',
        'description',
        'level', //beginner , intermadiate,advanced,elite
        'estimated_duration',
        'trainer_id'
    ];
}
