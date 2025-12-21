<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Routine;
use App\Models\User;
use App\Models\Plan;
use App\Models\AssignedRoutine;
use App\Models\WorkoutExerciseLog;

class AssignedRoutineController extends Controller
{
    // 1. Ver mis alumnos (Solo los que tiene asignados un entrenador)
    public function myStudents()
    {
        $trainerId = Auth::id();
        $students = User::where('assigned_trainer_id', $trainerId)
            ->where('role', 'client')
            ->get();

        return response()->json($students);
    }

    //listar planes creados por el entrenador logueado
    public function myPlans()
    {
        $trainerId = Auth::id();
        $plans = Plan::where('trainer_id', $trainerId)->get();

        return response()->json($plans);
    }
    //Listar rutinas creadas por el entrenador logueado
    public function myRoutines()
    {
        $trainerId = Auth::id();
        $routines = Routine::where('trainer_id', $trainerId)
            ->with('exercises')
            ->get();

        return response()->json($routines);
    }
    
    // Marcar rutina como completada y calificar (RF-18 y RF-23)
    public function complete(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $assignment = AssignedRoutine::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $assignment->update([
            'status' => 1, // 1 = Completado
            'rating' => $request->rating
        ]);

        return response()->json(['message' => '¡Entrenamiento completado! Gracias por calificar.']);
    }

    // Guardar el peso levantado por ejercicio (RF-17)
    public function logExerciseProgress(Request $request)
    {
        $request->validate([
            'workout_log_id' => 'required|exists:workout_logs,id',
            'exercise_id' => 'required|exists:exercises,id',
            'actual_sets' => 'required|integer',
            'actual_reps' => 'required|integer',
            'weight_used' => 'required|numeric',
        ]);

        $log = WorkoutExerciseLog::create($request->all());

        return response()->json(['message' => 'Progreso guardado', 'data' => $log]);
    }
}
