<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    // 1. LISTAR MIS PLANES (GET /api/plans) - Solo para entrenador autenticado
    public function index()
    {
        // Ahora que está protegido, Auth::id() siempre existe
        $planes = Plan::where('trainer_id', Auth::id())
            ->orderBy('id', 'asc')
            ->get();
        return response()->json($planes, 200);
    }

    // 1.1 LISTAR PLANES PÚBLICOS (GET /api/plans/public?trainer_id=X)
    public function publicIndex(Request $request)
    {
        $query = Plan::where('is_active', true);

        // Filtrar por entrenador si se especifica
        if ($request->has('trainer_id')) {
            $query->where('trainer_id', $request->trainer_id);
        }

        return response()->json($query->orderBy('price', 'asc')->get(), 200);
    }

    // 2. CREAR (POST /api/plans) - Opcional, ya que usamos la generación automática
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'trainer') {
            return response()->json(['message' => 'Solo los entrenadores pueden crear planes'], 403);
        }

        $request->validate([
            'type' => 'required|string|in:Basic,Pro,Personalized', // Solo estos 3 tipos
            'price' => 'required|numeric',
            'duration_days' => 'required|integer',
            'description' => 'required|string',
            'is_active' => 'boolean',
        ]);

        // Verificar si ya existe un plan de este tipo para este entrenador
        $existingPlan = Plan::where('trainer_id', Auth::id())
            ->where('type', $request->type)
            ->first();

        if ($existingPlan) {
            return response()->json([
                'message' => "Ya tienes un plan de tipo '{$request->type}'. Puedes editarlo en lugar de crear uno nuevo.",
                'existing_plan' => $existingPlan
            ], 422);
        }

        // Verificar que no tenga más de 3 planes en total
        $totalPlans = Plan::where('trainer_id', Auth::id())->count();
        if ($totalPlans >= 3) {
            return response()->json([
                'message' => 'Ya tienes los 3 planes permitidos (Basic, Pro, Premium). Solo puedes editarlos.'
            ], 422);
        }

        $plan = Plan::create([
            'name' => "Plan {$request->type}",
            'type' => $request->type,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'description' => $request->description,
            'is_active' => $request->is_active ?? false,
            'trainer_id' => Auth::id()
        ]);

        return response()->json([
            'message' => 'Plan creado con éxito',
            'data' => $plan
        ], 201);
    }

    // 3. MOSTRAR UNO (GET /api/plans/{id})
    public function show($id)
    {
        $plan = Plan::where('id', $id)->where('trainer_id', Auth::id())->first();

        if (!$plan) {
            return response()->json(['message' => 'Plan no encontrado'], 404);
        }

        return response()->json($plan, 200);
    }

    // 4. ACTUALIZAR (PUT /api/plans/{id}) - ¡CORREGIDO!
    public function update(Request $request, $id)
    {
        // 1. Buscar el plan y asegurar que pertenece al usuario
        $plan = Plan::where('id', $id)
            ->where('trainer_id', Auth::id())
            ->first();

        if (!$plan) {
            return response()->json(['message' => 'No autorizado o no encontrado'], 404);
        }

        // 2. Validar solo los campos que se envíen (update parcial)
        $request->validate([
            'price' => 'sometimes|nullable|numeric|min:0',
            'description' => 'sometimes|nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
        ]);

        // 3. Actualizar solo los campos que vienen en el request
        $plan->update($request->only(['price', 'description', 'is_active']));

        return response()->json([
            'message' => 'Plan actualizado con éxito',
            'data' => $plan
        ], 200);
    }

    // 5. ELIMINAR (DELETE /api/plans/{id})
    public function destroy($id)
    {
        $plan = Plan::where('id', $id)->where('trainer_id', Auth::id())->first();

        if (!$plan) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $plan->delete();
        return response()->json(['message' => 'Plan eliminado'], 200);
    }
}