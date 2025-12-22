<?php

namespace App\Http\Controllers;

use App\Models\AssignedDiet;
use App\Models\User; // Importante para validar el alumno
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignedDietController extends Controller
{

    // GET: Ver la dieta actual de un usuario
    public function showUserDiet()
    {
        // Buscamos la asignación más reciente que no haya vencido
        $assignment = AssignedDiet::where('user_id', Auth::id())
            ->with(['dietPlan.meals', 'dietPlan']) // Traemos info del plan y quien lo creó
            ->orderBy('start_date', 'desc')
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'El usuario no tiene dieta asignada actualmente'], 404);
        }

        return response()->json($assignment, 200);
    }
}
