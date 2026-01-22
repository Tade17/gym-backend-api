<?php

namespace App\Http\Controllers;

use App\Models\AssignedRoutine;
use App\Models\WorkoutLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientController extends Controller
{
    /**
     * Obtener lista de clientes con filtro opcional por tipo de plan
     * GET /api/clients?plan_type=Personalizado
     */
    public function index(Request $request)
    {
        $trainerId = Auth::id();

        $query = User::where('role', 'client')
            ->where('assigned_trainer_id', $trainerId)
            ->with([
                'subscriptions' => function ($q) {
                    $q->where('status', 1)->latest()->with('plan');
                }
            ]);

        // Filtrar por tipo de plan si se especifica
        if ($request->has('plan_type') && $request->plan_type) {
            $planType = $request->plan_type;

            $query->whereHas('subscriptions', function ($q) use ($planType) {
                $q->where('status', 1)
                    ->whereHas('plan', function ($p) use ($planType) {
                        // Buscar por type o por nombre del plan
                        $p->where('type', $planType)
                            ->orWhere('name', 'LIKE', "%{$planType}%");
                    });
            });
        }

        $clients = $query->get()->map(function ($client) {
            $subscription = $client->subscriptions->first();
            return [
                'id' => $client->id,
                'name' => $client->first_name . ' ' . $client->last_name,
                'email' => $client->email,
                'goal' => $client->goals,
                'plan' => $subscription?->plan?->name ?? 'Sin plan',
                'plan_type' => $subscription?->plan?->type ?? null,
            ];
        });

        return response()->json(['data' => $clients]);
    }

    // 1. Obtener la rutina asignada para HOY
    public function todayRoutine()
    {
        $today = Carbon::today()->format('Y-m-d');


        // Buscamos la rutina asignada al alumno para la fecha actual
        $assignment = AssignedRoutine::where('user_id', Auth::id())
            ->where('assigned_date', $today)
            ->with([
                'routine.exercises' => function ($query) {
                    $query->withPivot('sets', 'reps', 'rest_time');
                }
            ])
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'No tienes rutina asignada para hoy. ¡Día de descanso!'], 200);
        }

        return response()->json($assignment, 200);
    }


}