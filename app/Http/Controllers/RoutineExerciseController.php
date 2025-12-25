<?php

namespace App\Http\Controllers;

use App\Models\RoutineExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Routine;

class RoutineExerciseController extends Controller
{
    // POST: Agregar un ejercicio a una rutina
    public function store(Request $request, $routineId)
    {
        // 1. Verificamos que la rutina exista Y QUE SEA MÍA
        $routine = Routine::where('id', $routineId)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$routine) {
            return response()->json([
                'message' => 'Rutina no encontrada o no tienes permiso para editarla'
            ], 404);
        }

        // 2. Validamos los datos de entrada
        $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'sets' => 'required|integer|min:1',
            'reps' => 'required|integer|min:1',
            'rest_time' => 'required|integer|min:1'
        ]);

        // 3. Verificamos si ya existe ese ejercicio en la rutina para no duplicar
        $exists = RoutineExercise::where('routine_id', $routineId)
            ->where('exercise_id', $request->exercise_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Este ejercicio ya está en la rutina'], 409);
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
    public function destroy($routineId, $exerciseId)
    {
        // 1. Verificamos que la rutina exista Y QUE SEA MÍA
        $routine = Routine::where('id', $routineId)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$routine) {
            return response()->json(['message' => 'Rutina no encontrada o no tienes permiso'], 404);
        }

        // 2. Buscamos el registro específico en la tabla pivote
        $pivot = RoutineExercise::where('routine_id', $routineId)
            ->where('exercise_id', $exerciseId)
            ->first();

        if (!$pivot) {
            return response()->json(['message' => 'Ese ejercicio no forma parte de esta rutina'], 404);
        }

        // 3. Eliminamos la relación
        $pivot->delete();

        return response()->json(['message' => 'Ejercicio removido de la rutina con éxito'], 200);
    }
}
