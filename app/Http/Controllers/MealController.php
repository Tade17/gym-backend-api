<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Importante para la transacción
class MealController extends Controller
{
    public function index()
    {
        // Usamos 'food' porque así llamaste a la relación en tu modelo Meal
        $meals = Meal::with('food')->orderBy('id', 'desc')->get();
        return response()->json($meals, 200);
    }

    // POST /api/meals
    public function store(Request $request)
    {
        // 1. Validamos que vengan los datos básicos Y el array de alimentos
        $request->validate([
            'name' => 'required|string|unique:meals,name',
            'description' => 'nullable|string',
            'foods' => 'required|array|min:1', // Al menos 1 ingrediente
            'foods.*.id' => 'required|exists:food,id', // Validamos que el alimento exista
            'foods.*.quantity' => 'required|string' // Ej: "100g"
        ]);

        try {
            // Usamos una transacción: O se guarda todo, o no se guarda nada.
            return DB::transaction(function () use ($request) {
                
                // A. Crear la Comida (Cabecera)
                $meal = Meal::create([
                    'name' => $request->name,
                    'description' => $request->description,
                ]);

                // B. Asociar los alimentos en la tabla pivote (meal_food)
                foreach ($request->foods as $foodItem) {
                    // Usamos la relación 'food' definida en tu Modelo
                    $meal->food()->attach($foodItem['id'], [
                        'quantity' => $foodItem['quantity']
                    ]);
                }

                // C. Devolver la comida con sus ingredientes cargados
                return response()->json([
                    'message' => 'Comida creada con éxito',
                    'data' => $meal->load('food')
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al guardar la comida: ' . $e->getMessage()], 500);
        }
    }

    // GET /api/meals/{id}
    public function show($id)
    {
        $meal = Meal::with('food')->find($id);

        if (!$meal) {
            return response()->json(['message' => 'Meal no encontrada'], 404);
        }

        return response()->json($meal, 200);
    }

    // PUT /api/meals/{id}
    public function update(Request $request, $id)
    {
        // Nota: Por ahora solo actualizamos nombre/descripción para simplificar.
        // Si quieres editar ingredientes, necesitaríamos lógica extra (sync).
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
