<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DietPlan;
use App\Models\Meal;
use Illuminate\Support\Facades\Auth;

class DietPlanMealController extends Controller
{

    // asignar comida a un plan
    public function store(Request $request, $dietPlanId)
    {
        $dietPlan = DietPlan::where('id', $dietPlanId)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$dietPlan) {
            return response()->json(['message' => 'Plan no encontrado o sin permiso'], 404);
        }

        $request->validate([
            'meal_id' => 'required|exists:meals,id',
            'suggested_time' => 'required',
            'meal_type' => 'required|string|in:breakfast,lunch,snack,dinner',
            'day_of_week' => 'required|string',
        ]);

        if ($dietPlan->meals()->where('meal_id', $request->meal_id)->exists()) {
            return response()->json([
                'message' => 'Esta comida ya está asignada al plan'
            ], 409);
        }

        $dietPlan->meals()->attach($request->meal_id, [
            'suggested_time' => $request->suggested_time,
            'meal_type' => $request->meal_type,
            'day_of_week' => $request->day_of_week,
        ]);

        return response()->json(['message' => 'Comida asignada al plan'], 201);
    }

    // ACTUALIZAR datos del pivote
    public function update(Request $request, $dietPlanId, $mealId)
    {
        $dietPlan = DietPlan::where('id', $dietPlanId)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$dietPlan) {
            return response()->json(['message' => 'Plan no encontrado o sin permiso'], 404);
        }

        if (!$dietPlan->meals()->where('meal_id', $mealId)->exists()) {
            return response()->json(['message' => 'Meal no asociada a este plan'], 404);
        }

        $request->validate([
            'suggested_time' => 'sometimes',
            'meal_type' => 'sometimes|string',
            'day_of_week' => 'sometimes|string',
        ]);

        $dietPlan->meals()->updateExistingPivot($mealId, $request->only([
            'suggested_time',
            'meal_type',
            'day_of_week'
        ]));

        return response()->json(['message' => 'Asignación actualizada'], 200);
    }

    // QUITAR meal del plan
    public function destroy($dietPlanId, $mealId)
    {

        $dietPlan = DietPlan::where('id', $dietPlanId)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$dietPlan) {
            return response()->json(['message' => 'Plan no encontrado o sin permiso'], 404);
        }

        $exists = $dietPlan->meals()->where('meals.id', $mealId)->exists();

        if (!$exists) {
            return response()->json(['message' => 'La comida especificada no forma parte de este plan'], 404);
        }
        
        $dietPlan->meals()->detach($mealId);

        return response()->json(['message' => 'Meal removida del plan'], 200);
    }
}
