<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssignedRoutine;
use App\Models\WorkoutLog;
use App\Models\WorkoutExerciseLog;
use Illuminate\Support\Facades\Auth;

class StudentProgressController extends Controller
{
    /**
     * RF-17: Registrar pesos y repeticiones de un ejercicio específico
     * 
     * Este método guarda el progreso de UN ejercicio dentro de una sesión de entrenamiento.
     * Requiere que previamente se haya iniciado un WorkoutLog (sesión).
     */
    public function logExercise(Request $request)
    {
        $request->validate([
            'workout_log_id' => 'required|exists:workout_logs,id',
            'exercise_id' => 'required|exists:exercises,id',
            'actual_sets' => 'required|integer|min:1',
            'actual_reps' => 'required|integer|min:1',
            'weight_used' => 'required|numeric|min:0',
        ]);

        // Verificar que el workout_log pertenece al usuario actual
        $workoutLog = WorkoutLog::where('id', $request->workout_log_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$workoutLog) {
            return response()->json(['message' => 'Log de entrenamiento no encontrado o no te pertenece'], 404);
        }

        // Guardar el progreso del ejercicio
        $log = WorkoutExerciseLog::create([
            'workout_log_id' => $request->workout_log_id,
            'exercise_id' => $request->exercise_id,
            'actual_sets' => $request->actual_sets,
            'actual_reps' => $request->actual_reps,
            'weight_used' => $request->weight_used,
        ]);

        return response()->json([
            'message' => 'Progreso del ejercicio guardado',
            'data' => $log
        ], 201);
    }

    /**
     * Completar rutina con calificación (RF-18, RF-23)
     */
    public function completeRoutine(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $assignment = AssignedRoutine::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'Rutina asignada no encontrada'], 404);
        }

        $assignment->update([
            'status' => 1, // Completada
            'rating' => $request->rating,
        ]);

        return response()->json([
            'message' => '¡Rutina completada con éxito!',
            'data' => $assignment
        ]);
    }
}

