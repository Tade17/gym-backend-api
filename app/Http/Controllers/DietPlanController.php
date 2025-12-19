<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DietPlan;
use Illuminate\Support\Facades\Auth;


class DietPlanController extends Controller
{
    // Ver mis planes de dieta
    public function index()
    {
        return response()->json(DietPlan::where('trainer_id', Auth::id())->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'goal' => 'required|string',
            'plan_id' => 'nullable|exists:plans,id'
        ]);

        $diet = DietPlan::create([
            'name' => $request->name,
            'description' => $request->description,
            'goal' => $request->goal,
            'trainer_id' => Auth::id(),
            'plan_id' => $request->plan_id
        ]);

        return response()->json($diet, 201);
    }

    public function show($id)
    {
        $diet = DietPlan::with('trainer')->find($id); //ws

        if (!$diet) {
            return response()->json(['message' => 'Dieta no encontrada'], 404);
        }

        if ($diet->trainer_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para ver esta dieta'], 403);
        }

        return response()->json($diet, 200);
    }

    // ASIGNAR COMIDA A DIETA (Lógica Pivot)
    public function addMeal(Request $request, $dietId)
    {
        $diet = DietPlan::where('id', $dietId)->where('trainer_id', Auth::id())->firstOrFail();

        $request->validate([
            'meal_id' => 'required|exists:meals,id',
            'suggested_time' => 'required',
            'meal_type' => 'required|in:breakfast,brunch,lunch,snack,dinner',
            'day_of_week' => 'required|date'
        ]);

        $diet->meals()->attach($request->meal_id, [
            'suggested_time' => $request->suggested_time,
            'meal_type' => $request->meal_type,
            'day_of_week' => $request->day_of_week
        ]);

        return response()->json(['message' => 'Comida agregada a la dieta']);
    }
    // DELETE: Borrar dieta
    public function destroy($id)
    {
        $diet = DietPlan::where('trainer_id', Auth::id())->find($id); // Solo borro si es MÍA

        if (!$diet) {
            return response()->json(['message' => 'Dieta no encontrada o no tienes permiso'], 404);
        }

        $diet->delete();
        return response()->json(['message' => 'Dieta eliminada'], 200);
    }
}
