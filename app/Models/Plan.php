<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    // IMPORTANTE: Al poner SoftDeletes aquí, Laravel sabe que
    // cuando borremos algo, no debe eliminar la fila, solo marcar 'deleted_at'
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'description',
        'is_active',
        'trainer_id'
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
