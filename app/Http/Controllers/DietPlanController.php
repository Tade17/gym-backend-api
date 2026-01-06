<?php

namespace App\Http\Controllers;

use App\Models\DietPlan;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DietPlanController extends Controller
{
    // GET /api/diet-plans
    public function index()
    {
        $dietPlans = DietPlan::where('trainer_id', Auth::id())
            ->with('meals.food') 
            ->get();
        return response()->json($dietPlans, 200);
    }



    // POST /api/diet-plans
public function store(Request $request)
{
    // 1. Validación extendida para recibir la estructura del plan
    $request->validate([
        'name' => 'required|string',
        'goal' => 'required|string',
        'description' => 'nullable|string',
        'days' => 'required|array', // Se espera un objeto con días (monday, tuesday...)
    ]);

    try {
        return DB::transaction(function () use ($request) {
            // A. Crear el Plan Maestro
            $dietPlan = DietPlan::create([
                'name' => $request->name,
                'description' => $request->description,
                'goal' => $request->goal,
                'trainer_id' => Auth::id(),
            ]);

            // B. Procesar cada día (monday, tuesday, etc.)
            foreach ($request->days as $dayName => $mealsInDay) {
                foreach ($mealsInDay as $mealData) {
                    
                    // C. Crear la 'Meal' técnica para este bloque horario
                    $meal = Meal::create([
                        'name' => "Comida de {$dietPlan->name} - {$dayName}",
                        'description' => "Generada automáticamente"
                    ]);

                    // D. Asociar Alimentos a esta Meal (Tabla meal_food)
                    foreach ($mealData['foods'] as $foodItem) {
                        $meal->food()->attach($foodItem['id'], [
                            'quantity' => $foodItem['quantity']
                        ]);
                    }

                    // E. Vincular la Meal al Plan de Dieta (Tabla diet_plan_meal)
                    $dietPlan->meals()->attach($meal->id, [
                        'suggested_time' => $mealData['time'] ?? '08:00:00',
                        'meal_type' => $mealData['type'], // breakfast, lunch...
                        'day_of_week' => $dayName,       // monday, tuesday...
                    ]);
                }
            }

            return response()->json([
                'message' => 'Plan de dieta y estructura creados con éxito',
                'data' => $dietPlan->load('meals.food')
            ], 201);
        });
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
    }
}

    // GET /api/diet-plans/{id}
    public function show($id)
    {
        $dietPlan = DietPlan::where('id', $id)
            ->where('trainer_id', Auth::id())
            ->with('meals.food')
            ->first();

        if (!$dietPlan) {
            return response()->json([
                'message' => 'Plan no encontrado o sin permiso'
            ], 404);
        }

        return response()->json($dietPlan, 200);
    }

    // PUT /api/diet-plans/{id}
    public function update(Request $request, $id)
    {
        $dietPlan = DietPlan::where('id', $id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$dietPlan) {
            return response()->json([
                'message' => 'Plan no encontrado o sin permiso'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|string' . $id,
            'description' => 'nullable|string',
            'goal' => 'sometimes|string',
        ]);

        $dietPlan->update(
            $request->only(['name', 'description', 'goal'])
        );

        return response()->json([
            'message' => 'Plan de dieta actualizado',
            'data' => $dietPlan
        ], 200);
    }

    // DELETE /api/diet-plans/{id}
    public function destroy($id)
    {
        $dietPlan = DietPlan::where('id', $id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$dietPlan) {
            return response()->json([
                'message' => 'Plan no encontrado o sin permiso'
            ], 404);
        }


        $dietPlan->delete();

        return response()->json([
            'message' => 'Plan de dieta eliminado'
        ], 200);
    }
}
