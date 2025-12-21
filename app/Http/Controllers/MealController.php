<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function index()
    {
        $meals = Meal::all();
        return response()->json($meals, 200);
    }

    // POST /api/meals
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:meals,name',
            'description' => 'nullable|string',
        ]);

        $meal = Meal::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Meal creada con éxito',
            'data' => $meal
        ], 201);
    }

    // GET /api/meals/{id}
    public function show($id)
    {
        $meal = Meal::find($id);

        if (!$meal) {
            return response()->json(['message' => 'Meal no encontrada'], 404);
        }

        return response()->json($meal, 200);
    }

    // PUT /api/meals/{id}
    public function update(Request $request, $id)
    {
        $meal = Meal::find($id);

        if (!$meal) {
            return response()->json(['message' => 'Meal no encontrada'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|unique:meals,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $meal->update($request->only(['name', 'description']));

        return response()->json([
            'message' => 'Meal actualizada',
            'data' => $meal
        ], 200);
    }

    // DELETE /api/meals/{id}
    public function destroy($id)
    {
        $meal = Meal::find($id);

        if (!$meal) {
            return response()->json(['message' => 'Meal no encontrada'], 404);
        }

        $meal->delete();

        return response()->json([
            'message' => 'Meal eliminada'
        ], 200);
    }
}
