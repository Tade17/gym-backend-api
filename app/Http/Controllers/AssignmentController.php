<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DietPlan;
use App\Models\Routine;
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
            'goal' => 'nullable|string'
        ]);

        if (Auth::user()->role !== 'trainer') {
            return response()->json(['message' => 'Solo los trainers pueden asignar rutinas'], 403);
        }

        // Validar que la rutina pertenezca al trainer
        $routine = Routine::where('id', $request->routine_id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$routine) {
            return response()->json(['message' => 'La rutina no te pertenece'], 403);
        }

        $userIds = User::whereHas('subscriptions', function ($query) use ($request) {
            $query->where('plan_id', $request->plan_id)
                ->where('status', 1);
        })
        // NUEVO: Filtro por el texto del objetivo en el perfil del usuario
        ->when($request->goal, function ($query) use ($request) {
            return $query->where('goals', 'LIKE', '%' . $request->goal . '%');
        })
        ->pluck('id');

        if ($userIds->isEmpty()) {
            return response()->json(['message' => 'No hay alumnos que coincidan con el plan y objetivo seleccionados'], 404);
        }

        DB::transaction(function () use ($userIds, $request) {

            // Evitar duplicados
            AssignedRoutine::where('routine_id', $request->routine_id)
                ->whereIn('user_id', $userIds)
                ->delete();

            $data = $userIds->map(function ($id) use ($request) {
                return [
                    'user_id' => $id,
                    'routine_id' => $request->routine_id,
                    'assigned_date' => $request->assigned_date,
                    'trainer_id' => Auth::id(),
                    'status' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            AssignedRoutine::insert($data);
        });

        return response()->json([
            'message' => 'Rutina asignada correctamente a los alumnos'
        ], 200);
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

        if (Auth::user()->role !== 'trainer') {
            return response()->json(['message' => 'Solo los trainers pueden asignar dietas'], 403);
        }

        // Validar que el diet plan pertenezca al trainer
        $dietPlan = DietPlan::where('id', $request->diet_plan_id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$dietPlan) {
            return response()->json(['message' => 'El plan de dieta no te pertenece'], 403);
        }

        $userIds = User::whereHas('subscriptions', function ($query) use ($request) {
            $query->where('plan_id', $request->plan_id)
                ->where('status', 1);
        })->pluck('id');

        if ($userIds->isEmpty()) {
            return response()->json(['message' => 'No hay alumnos activos en este plan'], 404);
        }

        DB::transaction(function () use ($userIds, $request) {

            // Evitar duplicados
            AssignedDiet::where('diet_plan_id', $request->diet_plan_id)
                ->whereIn('user_id', $userIds)
                ->delete();

            $data = $userIds->map(function ($id) use ($request) {
                return [
                    'user_id' => $id,
                    'diet_plan_id' => $request->diet_plan_id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'trainer_id' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            AssignedDiet::insert($data);
        });

        return response()->json([
            'message' => 'Dieta asignada correctamente a los alumnos'
        ], 200);
    }

    /**
     * RF-08: Asignación personalizada de rutina a UN alumno
     */
    public function assignRoutineToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'routine_id' => 'required|exists:routines,id',
            'assigned_date' => 'required|date',
        ]);

        if (Auth::user()->role !== 'trainer') {
            return response()->json(['message' => 'Solo los trainers pueden asignar rutinas'], 403);
        }

        $routine = Routine::where('id', $request->routine_id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$routine) {
            return response()->json(['message' => 'La rutina no te pertenece'], 403);
        }

        $user = User::where('id', $request->user_id)
            ->where('assigned_trainer_id', Auth::id())
            ->firstOrFail();

        AssignedRoutine::where('routine_id', $routine->id)
            ->where('user_id', $user->id)
            ->delete();

        $assignment = AssignedRoutine::create([
            'user_id' => $user->id,
            'routine_id' => $routine->id,
            'assigned_date' => $request->assigned_date,
            'trainer_id' => Auth::id(),
            'status' => 0
        ]);

        return response()->json([
            'message' => 'Rutina asignada correctamente al alumno',
            'data' => $assignment
        ], 200);
    }

    /**
     * RF-12: Asignación personalizada de dieta a UN alumno
     */
    public function assignDietToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'diet_plan_id' => 'required|exists:diet_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if (Auth::user()->role !== 'trainer') {
            return response()->json(['message' => 'Solo los trainers pueden asignar dietas'], 403);
        }

        $dietPlan = DietPlan::where('id', $request->diet_plan_id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$dietPlan) {
            return response()->json(['message' => 'El plan de dieta no te pertenece'], 403);
        }

        $user = User::where('id', $request->user_id)
            ->where('assigned_trainer_id', Auth::id())
            ->firstOrFail();

        AssignedDiet::where('diet_plan_id', $dietPlan->id)
            ->where('user_id', $user->id)
            ->delete();

        $assignment = AssignedDiet::create([
            'user_id' => $user->id,
            'diet_plan_id' => $dietPlan->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'trainer_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Dieta asignada correctamente al alumno',
            'data' => $assignment
        ], 200);
    }
}
