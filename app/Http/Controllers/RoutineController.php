<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Routine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoutineController extends Controller
{
    public function index()
    {
        $routines = Routine::where('trainer_id', Auth::id())
            ->with('exercises')
            ->get();
        // Devolvemos en formato { data: [...] } para consistencia con tu Frontend
        return response()->json(['data' => $routines], 200);    }

    // POST: Crear rutina
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'trainer') {
            return response()->json(['message' => 'Solo los trainers pueden crear rutinas'], 403);
        }

        $validated=$request->validate([
            'name' => 'required|string|unique:routines,name',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'estimated_duration' => 'required|integer|min:1',
            
            // Validamos el array de ejercicios que viene del Frontend
            'exercises' => 'required|array|min:5',
            'exercises.*.id' => 'required|exists:exercises,id',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|integer|min:1',
            'exercises.*.rest_time' => 'required|integer|min:0',
        ]);

        // 2. TRANSACCIÓN
        // AQUÍ ESTABA EL ERROR: Agregamos $validated al use()
        return DB::transaction(function () use ($request, $validated) {
            
            // A. Crear la Rutina Padre
            $routine = Routine::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'level' => $validated['level'],
                'estimated_duration' => $validated['estimated_duration'],
                'trainer_id' => Auth::id(),
            ]);

            // B. Preparar los ejercicios permitiendo duplicados
            $exercisesData = [];
            foreach ($validated['exercises'] as $ex) {
                // Al NO usar el ID como clave del array ($exercisesData[]), 
                // permitimos que se agreguen múltiples entradas del mismo ejercicio.
                $exercisesData[] = [
                    'exercise_id' => $ex['id'],
                    'sets' => $ex['sets'],
                    'reps' => $ex['reps'],
                    'rest_time' => $ex['rest_time'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // B. Preparar los ejercicios (Cambiamos la estructura de $exercisesData)
            // $exercisesData = [];
            // foreach ($validated['exercises'] as $ex) {
            //     // En lugar de usar el ID como clave, creamos un array simple de objetos
            //     $exercisesData[] = [
            //         'exercise_id' => $ex['id'],
            //         'sets' => $ex['sets'],
            //         'reps' => $ex['reps'],
            //         'rest_time' => $ex['rest_time']
            //     ];
            // }

            // C. Insertar todos los ejercicios
            $routine->exercises()->attach($exercisesData);

            return response()->json([
                'message' => 'Rutina creada con éxito',
                'data' => $routine->load('exercises')
            ], 201);
        });
    }

    // PUT: Actualizar rutina y sincronizar ejercicios
    public function update(Request $request, $id)
    {
        $routine = Routine::where('id', $id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$routine) {
            return response()->json(['message' => 'Rutina no encontrada o no tienes permiso'], 404);
        }

        // 1. VALIDACIÓN UPDATE
        $validated = $request->validate([
            'name' => 'sometimes|string|unique:routines,name,' . $id,
            'description' => 'nullable|string',
            'level' => 'sometimes|in:beginner,intermediate,advanced',
            'estimated_duration' => 'sometimes|integer|min:1',
            // 'plan_id' => ... // ELIMINADO
            
            'exercises' => 'sometimes|array',
            'exercises.*.id' => 'required_with:exercises|exists:exercises,id',
            'exercises.*.sets' => 'required_with:exercises|integer|min:1',
            'exercises.*.reps' => 'required_with:exercises|integer|min:1',
            'exercises.*.rest_time' => 'required_with:exercises|integer|min:0',
        ]);

        // 2. TRANSACCIÓN UPDATE
        return DB::transaction(function () use ($routine, $request, $validated) {
            
            // A. Actualizar datos básicos (Sin plan_id)
            $routine->update($request->only(['name', 'description', 'level', 'estimated_duration']));

            // B. Sincronizar ejercicios
            if ($request->has('exercises')) {
                $exercisesData = [];
                foreach ($validated['exercises'] as $ex) {
                    $exercisesData[$ex['id']] = [
                        'sets' => $ex['sets'],
                        'reps' => $ex['reps'],
                        'rest_time' => $ex['rest_time']
                    ];
                }
                $routine->exercises()->sync($exercisesData);
            }

            return response()->json([
                'message' => 'Rutina actualizada correctamente', 
                'data' => $routine->load('exercises')
            ], 200);
        });
    }

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
