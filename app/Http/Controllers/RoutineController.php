<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Routine;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class RoutineController extends Controller
{
    public function index()
    {
        $routines = Routine::where('trainer_id', Auth::id())
            ->with('exercises')
            ->get();
        return response()->json($routines, 200);
    }

    // POST: Crear rutina
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'trainer') {
            return response()->json(['message' => 'Solo los trainers pueden crear rutinas'], 403);
        }

        $request->validate([
            'name' => 'required|string|unique:routines,name',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'estimated_duration' => 'required|integer|min:1',
            'plan_id' => 'nullable|exists:plans,id' // Para que el entrenador pueda crear rutinas y luego si quiere las asocia a un plan

        ]);
        //validar que el plan pertenezca al entrenador
        if ($request->plan_id) {
            $plan = Plan::where('id', $request->plan_id)
                ->where('trainer_id', Auth::id())
                ->first();
            if (!$plan) {
                return response()->json(['message' => 'El plan no te pertenece'], 403);
            }
        }
        $routine = Routine::create([
            'name' => $request->name,
            'description' => $request->description,
            'level' => $request->level,
            'estimated_duration' => $request->estimated_duration,
            'trainer_id' => Auth::id(),
            'plan_id' => $request->plan_id
        ]);

        return response()->json([
            'message' => 'Rutina creada con éxito',
            'data' => $routine
        ], 201);
    }

    // PUT: Actualizar solo si es MI rutina
    public function update(Request $request, $id)
    {
        $routine = Routine::where('id', $id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$routine) {
            return response()->json(['message' => 'Rutina no encontrada o no tienes permiso'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|unique:routines,name,' . $id,
            'description' => 'nullable|string',
            'level' => 'sometimes|in:beginner,intermediate,advanced',
            'estimated_duration' => 'sometimes|integer|min:1',
            'plan_id' => 'nullable|exists:plans,id'
        ]);
        //validar que el plan pertenezca al entrenador
        if ($request->plan_id) {
            $plan = Plan::where('id', $request->plan_id)
                ->where('trainer_id', Auth::id())
                ->first();
            if (!$plan) {
                return response()->json(['message' => 'El plan no te pertenece'], 403);
            }
        }
        $routine->update($request->all());

        return response()->json(['message' => 'Rutina actualizada', 'data' => $routine], 200);
    }

    // DELETE: Borrar solo si es MI rutina
    public function destroy($id)
    {
        $routine = Routine::where('id', $id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$routine) {
            return response()->json(['message' => 'No puedes borrar una rutina que no te pertenece'], 403);
        }

        $routine->delete();
        return response()->json(['message' => 'Rutina eliminada'], 200);
    }
}
