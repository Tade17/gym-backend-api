<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MealLog;
use App\Models\AssignedDiet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MealLogController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'diet_plan_meal_id' => 'required|exists:diet_plan_meal,id',
            'assigned_diet_id' => 'required|exists:assigned_diets,id',
            'consumed_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validación de imagen
            'notes' => 'nullable|string'
        ]);
        // Dentro del método store, después de la validación inicial:
        $assignedDiet = AssignedDiet::where('id', $request->assigned_diet_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$assignedDiet) {
            return response()->json(['message' => 'Esta asignación de dieta no te pertenece'], 403);
        }
        $photoPath = null;
        if ($request->hasFile('photo')) {
            // Guarda la foto en storage/app/public/meals
            $photoPath = $request->file('photo')->store('meals', 'public');
        }

        $log = MealLog::create([
            'user_id' => Auth::id(),
            'diet_plan_meal_id' => $request->diet_plan_meal_id,
            'assigned_diet_id' => $request->assigned_diet_id,
            'consumed_date' => $request->consumed_date,
            'photo_url' => $photoPath ? Storage::url($photoPath) : null,
            'is_completed' => 1,
            'notes' => $request->notes
        ]);

        return response()->json([
            'message' => 'Comida registrada con éxito',
            'data' => $log
        ], 201);
    }

    // Marcar/Desmarcar comida como completada (sin foto)
    public function toggleComplete(Request $request)
    {
        $request->validate([
            'diet_plan_meal_id' => 'required|exists:diet_plan_meal,id',
            'assigned_diet_id' => 'required|exists:assigned_diets,id',
        ]);

        // Verificar que la dieta pertenece al usuario
        $assignedDiet = AssignedDiet::where('id', $request->assigned_diet_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$assignedDiet) {
            return response()->json(['message' => 'Esta asignación de dieta no te pertenece'], 403);
        }

        $todayDate = now()->format('Y-m-d');

        // Buscar si ya existe un log para esta comida hoy
        $existingLog = MealLog::where('user_id', Auth::id())
            ->where('diet_plan_meal_id', $request->diet_plan_meal_id)
            ->where('assigned_diet_id', $request->assigned_diet_id)
            ->whereDate('consumed_date', $todayDate)
            ->first();

        if ($existingLog) {
            // Si existe, lo eliminamos (desmarcar)
            $existingLog->delete();
            return response()->json([
                'message' => 'Comida desmarcada',
                'is_completed' => false
            ], 200);
        } else {
            // Si no existe, lo creamos (marcar)
            MealLog::create([
                'user_id' => Auth::id(),
                'diet_plan_meal_id' => $request->diet_plan_meal_id,
                'assigned_diet_id' => $request->assigned_diet_id,
                'consumed_date' => $todayDate,
                'is_completed' => 1
            ]);
            return response()->json([
                'message' => 'Comida marcada como completada',
                'is_completed' => true
            ], 200);
        }
    }
}
