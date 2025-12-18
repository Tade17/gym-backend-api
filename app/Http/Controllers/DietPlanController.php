<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DietPlan;
use Illuminate\Support\Facades\Auth;


class DietPlanController extends Controller
{
    // GET: Ver MIS dietas (las que yo creé como entrenador)
    public function index()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Si soy admin, veo todas. Si soy entrenador, veo solo las mías.
        $query = DietPlan::query();

        //
        if ($user->role !== 'admin') {
            $query->where('trainer_id', $user->id);
        }

        return response()->json($query->get(), 200);
    }

    // POST: Crear nueva dieta
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'goal' => 'required|in:lose_weight,maintain,gain_muscle'
        ]);
        //Antes de crear la dieta debemos validar que la dieta se cree con el ID del entrenador autenticado 
        if (Auth::user()->role !== 'trainer') {
            return response()->json(['message' => 'Solo los entrenadores pueden crear planes de dieta'], 403);
        }

        $diet = DietPlan::create([
            'name' => $request->name,
            'description' => $request->description,
            'goal' => $request->goal,
            'trainer_id' => Auth::id() // <--- ¡AQUÍ ESTÁ LA MAGIA! Se guarda con TU firma(osea el id del entrenador).
        ]);

        return response()->json([
            'message' => 'Plan de dieta creado',
            'data' => $diet
        ], 201);
    }

    // GET: Ver una dieta específica
    public function show($id)
    {
        $diet = DietPlan::with('trainer')->find($id); // Traemos datos del creador

        if (!$diet) {
            return response()->json(['message' => 'Dieta no encontrada'], 404);
        }

        // Seguridad: Verificar si tengo permiso para verla (opcional, por ahora lo dejamos abierto)
        if ($diet->trainer_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para ver esta dieta'], 403);
        }

        return response()->json($diet, 200);
    }

    // DELETE: Borrar dieta
    public function destroy($id)
    {
        $diet = DietPlan::where('trainer_id', Auth::id())->find($id); // Solo borro si es MÍA

        if (!$diet) {
            return response()->json(['message' => 'Dieta no encontrada o no tienes permiso'], 404);
        }

        $diet->delete();
        return response()->json(['message' => 'Dieta eliminada'], 200);
    }
}
