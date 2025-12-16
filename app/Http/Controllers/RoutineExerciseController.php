<?php

namespace App\Http\Controllers;

use App\Models\RoutineExercise;
use App\Models\Routine;
use App\Models\Exercise;
use Illuminate\Http\Request;


class RoutineExerciseController extends Controller
{
    // POST: Agregar un ejercicio a una rutina
    // URL esperada: /api/routines/{id}/exercises
    public function store(Request $request, $routineId)
    {
        // 1. Verificamos que la rutina exista
        $routine = Routine::find($routineId);
        if (!$routine) {
            return response()->json(['message' => 'Rutina no encontrada'], 404);
        }

        // 2. Validamos los datos de entrada
        $request->validate([
            'exercise_id' => 'required|exists:exercises,id', // El ejercicio debe existir en la tabla exercises
            'sets' => 'required|integer|min:1',
            'reps' => 'required|integer|min:1',
            'rest_time' => 'required|integer|min:1' // Segundos
        ]);

        // 3. Verificamos si ya existe ese ejercicio en la rutina (para no duplicar)
        $exists = RoutineExercise::where('routine_id', $routineId)
                    ->where('exercise_id', $request->exercise_id)
                    ->exists();

        if ($exists) {
            return response()->json(['message' => 'Este ejercicio ya está en la rutina'], 409); // 409 Conflict
        }

        // 4. Creamos la unión
        $assignment = RoutineExercise::create([
            'routine_id' => $routineId,
            'exercise_id' => $request->exercise_id,
            'sets' => $request->sets,
            'reps' => $request->reps,
            'rest_time' => $request->rest_time
        ]);

        return response()->json([
            'message' => 'Ejercicio agregado a la rutina correctamente',
            'data' => $assignment
        ], 201);
    }
    
    // DELETE: Quitar un ejercicio de una rutina
    public function destroy($id)
    {
        $pivot = RoutineExercise::find($id);
        if (!$pivot) {
             return response()->json(['message' => 'Asignación no encontrada'], 404);
        }
        
        $pivot->delete();
        return response()->json(['message' => 'Ejercicio removido de la rutina'], 200);
    }
}
