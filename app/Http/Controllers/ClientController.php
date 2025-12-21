<?php

namespace App\Http\Controllers;

use App\Models\AssignedRoutine;
use App\Models\WorkoutLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientController extends Controller
{
    // 1. Obtener la rutina asignada para HOY
    public function todayRoutine()
    {
        $today = Carbon::today()->format('Y-m-d');
    

        // Buscamos la rutina asignada al alumno para la fecha actual
        $assignment = AssignedRoutine::where('user_id', Auth::id())
            ->where('assigned_date', $today)
            ->with(['routine.exercises' => function($query) {
                $query->withPivot('sets', 'reps', 'rest_time');
            }])
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'No tienes rutina asignada para hoy. ¡Día de descanso!'], 200);
        }

        return response()->json($assignment, 200);
    }

    
}