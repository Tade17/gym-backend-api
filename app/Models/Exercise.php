<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    
    // Lista blanca de campos permitidos
    protected $fillable = [
        'name',
        'description',
        'muscle_group',
        'video_url'
    ];
}
