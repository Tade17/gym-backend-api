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
        // Traemos el plan -> sus dias/horas -> la comida -> los alimentos
        $plans = DietPlan::where('trainer_id', Auth::id())
            ->with(['dietPlanMeals.meal.food'])
            ->get();

        return response()->json($plans);
    }



    // POST /api/diet-plans
    public function store(Request $request)
    {
        // 1. Validación - days es opcional para permitir crear planes vacíos
        $request->validate([
            'name' => 'required|string',
            'goal' => 'required|string',
            'description' => 'nullable|string',
            'days' => 'sometimes|array', // Opcional - se pueden agregar comidas después
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

                // B. Solo procesar días si vienen en el request
                if ($request->has('days') && !empty($request->days)) {
                    foreach ($request->days as $dayName => $mealsInDay) {
                        foreach ($mealsInDay as $mealData) {

                            // C. Crear la 'Meal' técnica para este bloque horario
                            $meal = Meal::create([
                                'name' => "Comida de {$dietPlan->name} - {$dayName}",
                                'description' => "Generada automáticamente"
                            ]);

                            // D. Asociar Alimentos a esta Meal (Tabla meal_food)
                            if (!empty($mealData['foods'])) {
                                foreach ($mealData['foods'] as $foodItem) {
                                    $meal->food()->attach($foodItem['id'], [
                                        'quantity' => $foodItem['quantity']
                                    ]);
                                }
                            }

                            // E. Vincular la Meal al Plan de Dieta (Tabla diet_plan_meal)
                            $dietPlan->meals()->attach($meal->id, [
                                'suggested_time' => $mealData['time'] ?? '08:00:00',
                                'meal_type' => $mealData['type'] ?? 'meal',
                                'day_of_week' => $dayName,
                            ]);
                        }
                    }
                }

                return response()->json([
                    'message' => 'Plan de dieta creado con éxito',
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
            'name' => 'sometimes|string|unique:diet_plans,name,' . $id,
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
