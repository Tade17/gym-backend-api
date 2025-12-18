<?php

namespace App\Http\Controllers;

use App\Models\AssignedDiet;
use App\Models\User; // Importante para validar el alumno
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignedDietController extends Controller
{
    // POST: Asignar dieta a un cliente
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'diet_plan_id' => 'required|exists:diet_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        // (Opcional) Validación extra: Verificar que el alumno sea MÍO
        // Si quieres ser estricto, descomenta esto:
        
        $student = User::where('id', $request->user_id)
                       ->where('assigned_trainer_id', Auth::id())
                       ->first();
        if (!$student) {
             return response()->json(['message' => 'Este alumno no te pertenece'], 403);
        }
        

        // Crear la asignación
        $assignment = AssignedDiet::create([
            'user_id' => $request->user_id,
            'diet_plan_id' => $request->diet_plan_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);

        return response()->json([
            'message' => 'Dieta asignada correctamente',
            'data' => $assignment
        ], 201);
    }

    // GET: Ver la dieta actual de un usuario
    public function showUserDiet($userId)
    {
        // Buscamos la asignación más reciente que no haya vencido
        $assignment = AssignedDiet::where('user_id', $userId)
            ->with(['dietPlan.trainer', 'dietPlan']) // Traemos info del plan y quien lo creó
            ->orderBy('start_date', 'desc')
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'El usuario no tiene dieta asignada actualmente'], 404);
        }

        return response()->json($assignment, 200);
    }
}
