<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AssignedRoutine;
use App\Models\AssignedDiet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    /**
     * RF-07: Asignación masiva de rutinas por tipo de Plan
     */
    public function massAssignRoutine(Request $request)
    {
        $request->validate([
            'routine_id' => 'required|exists:routines,id',
            'plan_id' => 'required|exists:plans,id',
            'assigned_date' => 'required|date',
        ]);

        //validar que sea entrenador
        if (Auth::user()->role !== 'trainer') {
            return response()->json(['message' => 'Solo los trainers asignar rutinas'], 403);
        }

        // 1. Buscamos alumnos con suscripción ACTIVA al plan elegido
        $userIds = User::whereHas('subscriptions', function ($query) use ($request) {
            $query->where('plan_id', $request->plan_id)
                ->where('status', 1); // 1 = Activa
        })->pluck('id');

        if ($userIds->isEmpty()) {
            return response()->json(['message' => 'No hay alumnos activos en este plan'], 404);
        }

        // 2. Insertamos masivamente para ahorrar recursos (Batch Insert)
        $data = $userIds->map(function ($id) use ($request) {
            return [
                'user_id' => $id,
                'routine_id' => $request->routine_id,
                'assigned_date' => $request->assigned_date,
                'trainer_id' => Auth::id(), // El entrenador que ejecuta la acción
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();


        AssignedRoutine::insert($data);

        return response()->json(['message' => "Rutina asignada a " . count($data) . " alumnos."]);
    }

    /**
     * RF-12: Asignación masiva de dietas por tipo de Plan
     */
    public function massAssignDiet(Request $request)
    {
        $request->validate([
            'diet_plan_id' => 'required|exists:diet_plans,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        //validar que sea entrenador
        if (Auth::user()->role !== 'trainer') {
            return response()->json(['message' => 'Solo los trainers pueden asignar dietas'], 403);
        }

        $userIds = User::whereHas('subscriptions', function ($query) use ($request) {
            $query->where('plan_id', $request->plan_id)
                ->where('status', 1);
        })->pluck('id');

        if ($userIds->isEmpty()) {
            return response()->json(['message' => 'No hay alumnos activos en este plan'], 404);
        }

        $data = $userIds->map(function ($id) use ($request) {
            return [
                'user_id' => $id,
                'diet_plan_id' => $request->diet_plan_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'trainer_id' => Auth::id(), // El entrenador que ejecuta la acción
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        AssignedDiet::insert($data);

        return response()->json(['message' => "Dieta asignada a " . count($data) . " alumnos."]);
    }

    /**
     * RF-08: Asignación personalizada de rutina a UN alumno específico
     */
    public function assignRoutineToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'routine_id' => 'required|exists:routines,id',
            'assigned_date' => 'required|date',
        ]);
        //validar si el que asigna es un entrenador
        if (Auth::user()->role !== 'trainer') {
            return response()->json(['message' => 'Solo los entrenadores pueden asignar rutinas'], 203);
        }
        // Opcional: Validar que el alumno esté a cargo de este entrenador
        $user = User::where('id', $request->user_id)
            ->where('assigned_trainer_id', Auth::id())
            ->firstOrFail();

        $assignment = AssignedRoutine::create([
            'user_id' => $user->id,
            'routine_id' => $request->routine_id,
            'assigned_date' => $request->assigned_date,
            'trainer_id' => Auth::id(),
            'status' => 0
        ]);

        return response()->json(['message' => 'Rutina asignada correctamente al alumno', 'data' => $assignment]);
    }


    /**
     * RF-12: Asignación personalizada de dieta a UN alumno específico
     */
    public function assignDietToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'diet_plan_id' => 'required|exists:diet_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        //validar que sea entrenador
        if (Auth::user()->role !== 'trainer') {
            return response()->json(['message' => 'Solo los entrenadores pueden asignar dietas'], 203);
        }

        $user = User::where('id', $request->user_id)
            ->where('assigned_trainer_id', Auth::id())
            ->firstOrFail();

        $assignment = AssignedDiet::create([
            'user_id' => $user->id,
            'diet_plan_id' => $request->diet_plan_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'trainer_id' => Auth::id()
        ]);

        return response()->json(['message' => 'Dieta asignada correctamente al alumno', 'data' => $assignment]);
    }
}
