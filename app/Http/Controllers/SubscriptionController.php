<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    // POST: Crear una suscripción (Compra de plan)
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        //Aca buscamos el usuario autenticado
        $user = User::find(Auth::id());

        $plan = Plan::find($request->plan_id);

        // 1. Calculamos las fechas
        $startDate = Carbon::now();
        $endDate = $startDate->copy()->addDays($plan->duration_days);

        //validamos si el usuario ya tiene una suscripcion activa
        $existingSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 1)
            ->first();
        if ($existingSubscription) {
            return response()->json(['message' => 'Ya tienes una suscripción activa.'], 400);
        }

        //validamos si el usuario es un cliente
        if ($user->role !== 'client') {
            return response()->json(['message' => 'Solo los clientes pueden suscribirse a un plan.'], 403);
        }

        // 2. Creamos la suscripción
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'status' => 1,
        ]);

        // 3. ¡VITAL! Asignamos al entrenador del plan como el entrenador del usuario
        // Así el entrenador podrá ver a este cliente en su lista.
        $user->update([
            'assigned_trainer_id' => $plan->trainer_id
        ]);

        return response()->json([
            'message' => '¡Suscripción exitosa!',
            'trainer_assigned' => $plan->trainer->first_name . ' ' . $plan->trainer->last_name,
            'expires_at' => $subscription->end_date,
            'data' => $subscription
        ], 201);
    }

    // GET: Ver mi suscripción actual (para el cliente)
    public function mySubscription()
    {
        $subscription = Subscription::where('user_id', Auth::id())
            ->with('plan.trainer')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'No tienes suscripciones activas'], 404);
        }

        return response()->json($subscription);
    }
}
