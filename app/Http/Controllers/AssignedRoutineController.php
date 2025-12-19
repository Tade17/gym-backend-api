<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; //Para obtener el usuario autenticado
use App\Models\AssignedRoutine;
use App\Models\Routine;
use App\Models\User;
use App\Models\Plan;

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

    // 2. Asignación Individual: Mandar rutina a UN alumno
    public function assignToStudent(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'routine_id' => 'required|exists:routines,id',
            'assigned_date' => 'required|date',
        ]);

        //verificamos que tenga suscripcion activa
        $student = User::where('id', $request->user_id)
            ->where('assigned_trainer_id', Auth::id())
            ->whereHas('subscription', function ($query) {
                $query->where('status', 'active');
            })->first();

        if (!$student) {
            return response()->json([
                'message' => 'El alumno no te pertenece o no tiene una suscripción activa.'
            ], 403);
        }

        $assignment = AssignedRoutine::create([
            'user_id' => $request->user_id,
            'routine_id' => $request->routine_id,
            'assigned_date' => $request->assigned_date,
            'status' => 0 // Pendiente
        ]);

        return response()->json(['message' => 'Rutina asignada con éxito', 'data' => $assignment], 201);
    }

    // 3. ENVÍO MASIVO: Mandar rutina a todos los de un PLAN
    public function massAssign(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'routine_id' => 'required|exists:routines,id',
            'assigned_date' => 'required|date',
        ]);

        // Buscamos alumnos del entrenador que tengan el plan activo
        // y les asignamos la rutina solo a las personas que tienen la suscripción activa con whereHas
        $students = User::where('assigned_trainer_id', Auth::id())
            ->whereHas('subscription', function ($query) use ($request) {
                $query->where('plan_id', $request->plan_id)
                    ->where('status', 'active');
            })->get();

        if ($students->isEmpty()) {
            return response()->json(['message' => 'No hay alumnos con ese plan activo'], 404);
        }

        foreach ($students as $student) {
            AssignedRoutine::create([
                'user_id' => $student->id,
                'routine_id' => $request->routine_id,
                'assigned_date' => $request->assigned_date,
                'status' => 0
            ]);
        }

        return response()->json([
            'message' => "Se asignó la rutina a " . $students->count() . " alumnos."
        ]);
    }
}
