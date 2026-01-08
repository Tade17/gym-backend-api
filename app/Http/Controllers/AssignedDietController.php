<?php

namespace App\Http\Controllers;

use App\Models\AssignedDiet;
use App\Models\User;
use App\Models\DietPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AssignedDietController extends Controller
{
    /**
     * Asignación Individual (Para planes personalizados)
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'diet_plan_id' => 'required|exists:diet_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // 1. Seguridad: Verificar que el plan pertenece al entrenador logueado
        $plan = DietPlan::where('id', $request->diet_plan_id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$plan) {
            return response()->json(['message' => 'No tienes permiso para asignar este plan.'], 403);
        }

        // 2. Limpieza: Borrar asignación previa de este mismo plan para no duplicar fechas
        AssignedDiet::where('user_id', $request->user_id)
            ->where('diet_plan_id', $request->diet_plan_id)
            ->delete();

        // 3. Crear
        $assignment = AssignedDiet::create([
            'user_id' => $request->user_id,
            'diet_plan_id' => $request->diet_plan_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'trainer_id' => Auth::id()
        ]);

        return response()->json(['message' => 'Plan asignado correctamente', 'data' => $assignment], 201);
    }

    /**
     * Asignación Masiva (Para planes Pro) - Cumple RF-07
     */
    public function storeMassive(Request $request)
    {
        $request->validate([
            'diet_plan_id' => 'required|exists:diet_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'target_plan_type' => 'required|string' // Ej: 'Pro', 'Personalized'
        ]);

        // 1. Seguridad: Verificar propiedad del plan
        $dietPlan = DietPlan::where('id', $request->diet_plan_id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$dietPlan) {
            return response()->json(['message' => 'No tienes permiso para asignar este plan.'], 403);
        }

        // 2. Buscar usuarios (Pro) que TAMBIÉN coincidan con el objetivo
        $targetUsers = User::whereHas('subscriptions', function($query) use ($request) {
            $query->where('status', 1)
                  ->whereHas('plan', function($q) use ($request) {
                      $q->where('type', $request->target_plan_type) 
                        ->orWhere('name', 'LIKE', '%' . $request->target_plan_type . '%');
                  });
        })
        ->where('role', 'client')
        // *** EL FILTRO CLAVE: Objetivo Usuario == Objetivo Plan ***
        ->where('goals', 'LIKE', '%' . $dietPlan->goal . '%') 
        ->get();

        if ($targetUsers->isEmpty()) {
            return response()->json(['message' => "No se encontraron alumnos '{$request->target_plan_type}' con el objetivo '{$dietPlan->goal}'."], 404);
        }

        $count = 0;
        DB::transaction(function () use ($targetUsers, $request, &$count) {
             $userIds = $targetUsers->pluck('id');
             
             // 1. LIMPIEZA INTELIGENTE:
             // Borramos CUALQUIER asignación de dieta futura o presente que choque con las fechas nuevas.
             // Así garantizamos que el alumno solo vea ESTE plan nuevo.
             AssignedDiet::whereIn('user_id', $userIds)
                ->where(function($q) use ($request) {
                    $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
                })
                ->delete();

             // 2. Insertar la nueva
             foreach ($targetUsers as $user) {
                AssignedDiet::create([
                    'user_id' => $user->id,
                    'diet_plan_id' => $request->diet_plan_id,
                    'start_date' => $request->start_date, 
                    'end_date' => $request->end_date,
                    'trainer_id' => Auth::id()
                ]);
                $count++;
            }
        });

        return response()->json(['message' => "Plan asignado a {$count} alumnos Pro con objetivo '{$dietPlan->goal}'"], 201);
    }

    // Ver dieta del usuario logueado (Para la App Móvil)
    public function showUserDiet()
    {
        $assignment = AssignedDiet::where('user_id', Auth::id())
            // Verificamos que la fecha actual esté dentro del rango
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with(['dietPlan.meals.food', 'dietPlan']) 
            ->orderBy('start_date', 'desc')
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'No tienes un plan de alimentación activo hoy.'], 404);
        }

        return response()->json($assignment, 200);
    }
}