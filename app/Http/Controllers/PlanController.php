<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    // 1. LISTAR todos (GET /api/plans)
    public function index()
    {
        // CORRECCIÓN: No filtramos por Auth::id() porque esta ruta es pública.
        // Y no usamos trainer_id porque no parece estar en tu tabla de la foto.
        $planes = Plan::where('is_active', 1)->get();
        
        return response()->json($planes, 200);
    }

    //  LISTAR por id (GET /api/plans/{id})
    public function show($id)
    {
        $plan = Plan::where('id', $id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$plan) {
            return response()->json(['message' => 'Plan no encontrado o no tienes permiso'], 404);
        }

        return response()->json($plan, 200);
    }

    // POST: Crear un nuevo plan
    // 2. CREAR uno nuevo (POST /api/plans)
    public function store(Request $request)
    {

        if (Auth::user()->role !== 'trainer') {
            return response()->json(['message' => 'Solo los entrenadores pueden crear planes'], 403);
        }
        // Validamos que envíen los datos necesarios
        $request->validate([
            'type' => 'required|string|in:basic,pro,personalized',
            'price' => 'required|numeric',
            'duration_days' => 'required|integer',
            'description' => 'required|string',
            'is_active' => 'sometimes|boolean',
        ]);

        // Límite de 3 planes ---
        $trainerId = Auth::id();
        $planCount = Plan::where('trainer_id', $trainerId)->count();

        if ($planCount >= 4) {
            return response()->json([
                'message' => 'Límite alcanzado. Solo puedes tener un máximo de 3 planes (Basico, Pro y Personalizado).'
            ], 400);
        }

        // Validar que no repita el mismo TIPO ---
        $existsType = Plan::where('trainer_id', $trainerId)
            ->where('type', $request->type)
            ->exists();

        if ($existsType) {
            return response()->json([
                'message' => "Ya tienes un plan de tipo '{$request->type}' creado."
            ], 400);
        }
        // --------------------------------------------

        // Creamos el plan en la BD
        $plan = Plan::create([
            'type' => $request->type,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
            'trainer_id' => Auth::id()
        ]);


        return response()->json([
            'message' => 'Plan creado con éxito',
            'data' => $plan
        ], 201);
    }

    //UPDATE:Actualizar un plan (PUT /api/plans/{id})
    public function update(Request $request, $id)
    {
        $plan = Plan::where('id', $id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$plan) {
            return response()->json(['message' => 'Plan no encontrada o no tienes permiso'], 404);
        }
        //validamos los datos 
        $request->validate([
            'type' => 'sometimes|string|in:basic,pro,personalized',
            'price' => 'sometimes|numeric',
            'duration_days' => 'sometimes|integer',
            'description' => 'sometimes|string',
            'is_active' => 'sometimes|boolean',
        ]);

        // Validar que no repita el mismo TIPO ---
        $existsType = Plan::where('trainer_id', Auth::id())
            ->where('type', $request->type)
            ->exists();

        if ($existsType) {
            return response()->json([
                'message' => "Ya tienes un plan de tipo '{$request->type}' creado."
            ], 400);
        }
        //actualizamos el plan 
        $plan->update($request->only([
            'type',
            'price',
            'duration_days',
            'description',
            'is_active'
        ]));


        return response()->json([
            'message' => 'Plan actualizado con exito',
            'data' => $plan
        ], 200);
    }

    // DELETE: Eliminar un plan (DELETE /api/plans/{id})
    public function destroy($id)
    {
        $plan = Plan::where('id', $id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$plan) {
            return response()->json(['message' => 'Plan no encontrada o no tienes permiso'], 404);
        }

        $plan->delete();

        return response()->json([
            'message' => 'Plan eliminado con exito'
        ], 200);
    }
}
