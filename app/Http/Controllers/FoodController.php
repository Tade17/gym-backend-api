<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Food;

class FoodController extends Controller
{

    public function index()
    {
        $foods = Food::all();

        return response()->json($foods, 200);
    }

    public function show($id)
    {
        $food = Food::find($id);
        if (!$food) {
            return response()->json(['message' => 'Comida  no encontrada'], 404);
        }

        return response()->json($food, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:food,name',
            'calories_per_100g' => 'required|numeric|min:0',
        ]);

        $food = Food::create([
            'name' => $request->name,
            'calories_per_100g' => $request->calories_per_100g,
        ]);

        return response()->json([
            'message' => 'Comida creada con éxito',
            'data' => $food
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $food = Food::find($id);

        if (!$food) {
            return response()->json([
                'message' => 'Comida no encontrada'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:food,name,' . $id,
            'calories_per_100g' => 'sometimes|numeric|min:0',
        ]);

        $food->update($request->only([
            'name',
            'calories_per_100g'
        ]));

        return response()->json([
            'message' => 'Comida actualizada con éxito',
            'data' => $food
        ], 200);
    }

    public function destroy($id)
    {
        $food = Food::find($id);

        if (!$food) {
            return response()->json([
                'message' => 'Comida no encontrada'
            ], 404);
        }

        $food->delete();

        return response()->json([
            'message' => 'Comida eliminada con éxito'
        ], 200);
    }
}
