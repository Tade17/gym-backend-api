<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\DietPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
    // POST: Agregar una comida a un plan de dieta
    // URL: /api/diet-plans/{id}/meals
    public function store(Request $request, $dietPlanId)
    {
        // 1. Verificamos que el plan exista y sea mío (si soy entrenador)
        $dietPlan = DietPlan::where('id', $dietPlanId)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$dietPlan) {
            return response()->json(['message' => 'Plan de dieta no encontrado o acceso denegado'], 404);
        }

        $request->validate([
            'type' => 'required|in:breakfast,lunch,dinner,snack',
            'suggested_time' => 'required', // HH:MM:SS
            'description' => 'required|string'
        ]);

        $meal = Meal::create([
            'diet_plan_id' => $dietPlanId,
            'type' => $request->type,
            'suggested_time' => $request->suggested_time,
            'description' => $request->description
        ]);

        return response()->json(['message' => 'Comida agregada', 'data' => $meal], 201);
    }

    // GET: Ver las comidas de un plan
    public function index($dietPlanId)
    {
        $meals = Meal::where('diet_plan_id', $dietPlanId)->orderBy('suggested_time')->get();
        return response()->json($meals, 200);
    }
}
