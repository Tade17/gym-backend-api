<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssignedRoutine;
use App\Models\WorkoutLog;
use Illuminate\Support\Facades\Auth;

class StudentProgressController extends Controller
{
    /**
     * RF-17: Registrar pesos y repeticiones de un ejercicio
     */
    public function logExercise(Request $request)
    {
        $request->validate([
            'assigned_routine_id' => 'required|exists:assigned_routines,id',
            'weight_used' => 'required|numeric',
            'reps' => 'required|integer',
            'duration' => 'nullable|integer', // en minutos
            'notes' => 'nullable|string',
        ]);

        // Guardamos el log de entrenamiento
        $log = WorkoutLog::create([
            'assigned_routine_id' => $request->assigned_routine_id,
            'workout_date' => now()->format('Y-m-d'),
            'weight_used' => $request->weight_used,
            'reps' => $request->reps,
            'duration' => $request->duration,
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'Progreso guardado', 'log' => $log]);
    }

    /**
     * RF-18 y RF-23: Check-in y Calificación en estrellas
     */
    public function completeRoutine(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5', // Calificación de 5 estrellas
        ]);

        $assignment = AssignedRoutine::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $assignment->update([
            'status' => 1, // 1 = Completada
            'rating' => $request->rating,
        ]);

        return response()->json(['message' => '¡Rutina completada! Buen trabajo.']);
    }
}
