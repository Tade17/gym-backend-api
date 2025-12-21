<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Routine;
use App\Models\User;
use App\Models\Plan;
use App\Models\AssignedRoutine;
use App\Models\WorkoutExerciseLog;
use App\Models\WorkoutLog;

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

    //EMPEZAR ENTRENAMIENTO
    public function startWorkout(Request $request)
    {
        $request->validate([
            'assigned_routine_id' => 'required|exists:assigned_routines,id',
        ]);

        // Creamos la sesión de entrenamiento
        $workoutLog = WorkoutLog::create([
            'assigned_routine_id' => $request->assigned_routine_id,
            'user_id' => Auth::id(),
            'workout_date' => now(),
            'is_completed' => 0, // Aún está entrenando
        ]);

        return response()->json([
            'message' => 'Entrenamiento iniciado',
            'workout_log_id' => $workoutLog->id
        ], 201);
    }
    // Marcar rutina como completada y calificar (RF-18 y RF-23)
    public function complete(Request $request, $id)
    {
        $request->validate([
            'rating'     => 'required|integer|min:1|max:5',
            'duration'   => 'required|integer',
            'workout_log_id' => 'required|exists:workout_logs,id',
            'notes' => 'sometimes|string'
        ]);

        DB::transaction(function () use ($request, $id) {
            // 1. Actualizamos la asignación (RF-18 y RF-23)
            $assignment = AssignedRoutine::findOrFail($id);
            $assignment->update([
                'status' => 1, // Completado
                'rating' => $request->rating
            ]);

            // 2. Cerramos el Log de entrenamiento con la duración
            $workoutLog = WorkoutLog::findOrFail($request->workout_log_id);
            $workoutLog->update([
                'duration'     => $request->duration,
                'is_completed' => true, // Marcamos que la sesión terminó
                'notes' => $request->notes
            ]);
        });

        return response()->json(['message' => '¡Entrenamiento guardado y finalizado con éxito!']);
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
