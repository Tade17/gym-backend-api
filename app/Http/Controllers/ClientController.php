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

    // 2. Registrar el progreso de un ejercicio (Workout Log)
    public function logWorkout(Request $request)
    {
        $request->validate([
            'assigned_routine_id' => 'required|exists:assigned_routines,id',
            'weight_used' => 'required|numeric|min:0', 
            'reps' => 'required|integer|min:1',       
            'duration' => 'required|integer|min:1',   
            'notes' => 'nullable|string'
        ]);

        // Seguridad: Verificar que la rutina asignada realmente sea del alumno logueado
        $assignment = AssignedRoutine::where('id', $request->assigned_routine_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'Esta asignación no te pertenece'], 403);
        }

        // Crear el log de entrenamiento
        $log = WorkoutLog::create([
            'assigned_routine_id' => $request->assigned_routine_id,
            'workout_date' => Carbon::now()->format('Y-m-d'),
            'duration' => $request->duration,
            'weight_used' => $request->weight_used,
            'reps' => $request->reps,
            'notes' => $request->notes
        ]);

        // Marcar la rutina como completada (status = 1)
        $assignment->update(['status' => 1]);

        return response()->json([
            'message' => '¡Entrenamiento registrado con éxito!',
            'data' => $log
        ], 201);
    }
}