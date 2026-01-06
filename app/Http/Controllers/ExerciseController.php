<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exercise;

class ExerciseController extends Controller
{
    // GET: Listar ejercicios
    public function index()
    {
        return response()->json(Exercise::all(), 200);
    }

    // POST: Crear ejercicio nuevo
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:exercises,name',
            'description' => 'nullable|string',
            'muscle_group' => 'required|string',
            'video_url' => 'nullable|string'
        ]);

        $exercise = Exercise::create($request->all());

        return response()->json([
            'message' => 'Ejercicio creado con éxito',
            'data' => $exercise
        ], 201);
    }

    // GET: Ver un solo ejercicio
    public function show($id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            return response()->json(['message' => 'Ejercicio no encontrado'], 404);
        }
        return response()->json($exercise, 200);
    }

    // PUT: Actualizar
    public function update(Request $request, $id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            return response()->json(['message' => 'Ejercicio no encontrado'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|unique:exercises,name,'.$id,
            'description' => 'nullable|string',
            'muscle_group' => 'sometimes|string',
            'video_url' => 'sometimes|string'
        ]);

        $exercise->update($request->all());

        return response()->json([
            'message' => 'Ejercicio actualizado',
            'data' => $exercise
        ], 200);
    }

    // DELETE: Eliminar
    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);

    // Verificamos si el ejercicio está presente en la tabla pivot de rutinas
    // Asumiendo que la relación en tu modelo Exercise se llama 'routines'
    if ($exercise->routines()->exists()) {
        return response()->json([
            'message' => 'No se puede eliminar: Este ejercicio forma parte de una o más rutinas activas.',
            'error' => 'integrity_violation'
        ], 422); // Error de validación / conflicto
    }

    $exercise->delete();

    return response()->json([
        'message' => 'Ejercicio eliminado con éxito.'
    ], 200);
    }
}
