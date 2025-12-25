<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DietPlan;
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
        if (Auth::user()->role !== 'trainer') {
            return response()->json([
                'message' => 'Solo los trainers pueden crear planes de dieta'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'goal' => 'required|string',
        ]);

        // Validar que no repita el mismo TIPO ---
        $existsName = DietPlan::where('trainer_id', Auth::id())
            ->where('name', $request->name)
            ->exists();

        if ($existsName) {
            return response()->json([
                'message' => "Ya tienes un plan de dieta con este nombre '{$request->name}' creado."
            ], 400);
        }
        $dietPlan = DietPlan::create([
            'name' => $request->name,
            'description' => $request->description,
            'goal' => $request->goal,
            'trainer_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Plan de dieta creado con éxito',
            'data' => $dietPlan
        ], 201);
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
