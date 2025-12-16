<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Routine;

class RoutineController extends Controller
{
    public function index()
    {
        $routines = Routine::all();
        return response()->json($routines, 200);
    }
    // POST: Crear rutina 
    public function store(Request $request)
    {
        // Descomentar para verificar que el usuario autenticado sea trainer
        /*if (auth()->user()->role !== 'trainer') {
            return response()->json([
                'message' => 'Solo los trainers pueden crear rutinas'
            ], 403);
        }*/
        $request->validate([
            'name' => 'required|string|unique:routines,name',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced,elite', // Validamos que sea uno de estos
            'estimated_duration' => 'sometimes|integer|min:1',
            'trainer_id' => 'required|exists:users,id'
        ]);

        $routine = Routine::create($request->all());

        return response()->json([
            'message' => 'Rutina creada con éxito',
            'data' => $routine
        ], 201);
    }

    // GET: Ver una rutina específica
    public function show($id)
    {
        $routine = Routine::find($id);

        if (!$routine) {
            return response()->json(['message' => 'Rutina no encontrada'], 404);
        }
        return response()->json($routine, 200);
    }

    // PUT: Actualizar
    public function update(Request $request, $id)
    {
        $routine = Routine::find($id);

        if (!$routine) {
            return response()->json(['message' => 'Rutina no encontrada'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|unique:routines,name,' . $id,
            'description' => 'nullable|string',
            'level' => 'sometimes|in:beginner,intermediate,advanced,elite',
            'estimated_duration' => 'sometimes|integer|min:1',
            'trainer_id' => 'sometimes|exists:users,id'
        ]);

        $routine->update($request->all());

        return response()->json(['message' => 'Rutina actualizada', 'data' => $routine], 200);
    }

    // DELETE: Borrar
    public function destroy($id)
    {
        $routine = Routine::find($id);
        if (!$routine) {
            return response()->json(['message' => 'Rutina no encontrada'], 404);
        }
        $routine->delete();
        return response()->json(['message' => 'Rutina eliminada'], 200);
    }
}
