<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\MealFood;

class MealFoodController extends Controller
{
    public function store(Request $request, $mealId)
    {
        // 1. Verificar que la comida exista 
        $meal = Meal::where('id', $mealId)
            ->first();

        if (!$meal) {
            return response()->json([
                'message' => 'Comida no encontrada'
            ], 404);
        }

        // 2. Validar datos
        $request->validate([
            'food_id' => 'required|exists:food,id',
            'quantity' => 'required|numeric|min:1' // gramos
        ]);

        // 3. Evitar duplicados
        $exists = MealFood::where('meal_id', $mealId)
            ->where('food_id', $request->food_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Este alimento ya está agregado a la comida'
            ], 409);
        }

        // 4. Crear relación
        $pivot = MealFood::create([
            'meal_id' => $mealId,
            'food_id' => $request->food_id,
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'message' => 'Alimento agregado a la comida correctamente',
            'data' => $pivot
        ], 201);
    }

    public function destroy($mealId, $foodId)
    {
        $meal = Meal::where('id', $mealId)
            ->first();

        if (!$meal) {
            return response()->json([
                'message' => 'Comida no encontrada '
            ], 404);
        }

        // 2. Buscar pivot
        $pivot = MealFood::where('meal_id', $mealId)
            ->where('food_id', $foodId)
            ->first();

        if (!$pivot) {
            return response()->json([
                'message' => 'Ese alimento no forma parte de esta comida'
            ], 404);
        }

        $pivot->delete();

        return response()->json([
            'message' => 'Alimento removido de la comida con éxito'
        ], 200);
    }
}
