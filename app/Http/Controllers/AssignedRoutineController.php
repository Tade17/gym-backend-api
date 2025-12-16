<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssignedRoutine;
use Illuminate\Support\Facades\Auth; //Para obtener el usuario autenticado   

class AssignedRoutineController extends Controller
{
    // POST: Asignar una rutina a una fecha
    public function store(Request $request)
    {
        $request->validate([
            'routine_id' => 'required|exists:routines,id',
            'assigned_date' => 'required|date', // YYYY-MM-DD
        ]);

        // Verificamos que no tenga ya esa misma rutina asignada ese mismo día (para no duplicar)
        $exists = AssignedRoutine::where('user_id', Auth::id())
            ->where('routine_id', $request->routine_id)
            ->where('assigned_date', $request->assigned_date)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Ya tienes esta rutina asignada para ese día'], 409);
        }

        $assignment = AssignedRoutine::create([
            'user_id' => Auth::id(), // El usuario logueado
            'routine_id' => $request->routine_id,
            'assigned_date' => $request->assigned_date,
            'status' => 0 // 0 = Pendiente
        ]);

        return response()->json([
            'message' => 'Rutina agendada correctamente',
            'data' => $assignment
        ], 201);
    }

    // GET: Ver mi agenda (puede filtrar por fecha opcionalmente)
    // Ej: /api/my-schedule?date=2025-12-16
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = AssignedRoutine::where('user_id', $userId)
            ->with('routine'); // Traemos los datos de la rutina (nombre, nivel)

        // Si envían una fecha en la URL, filtramos por esa fecha
        if ($request->has('date')) {
            $query->where('assigned_date', $request->date);
        }

        $schedule = $query->orderBy('assigned_date', 'asc')->get();

        return response()->json($schedule, 200);
    }

    // PUT: Marcar como completada (Check!)
    public function complete(Request $request, $id)
    {
        $assignment = AssignedRoutine::where('user_id', Auth::id())->find($id);

        if (!$assignment) {
            return response()->json(['message' => 'Asignación no encontrada'], 404);
        }

        $assignment->update([
            'status' => 1, // 1 = Completado
            'rating' => $request->rating // Opcional: calificar del 1 al 5
        ]);

        return response()->json(['message' => '¡Rutina completada! Buen trabajo.'], 200);
    }
}
