<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;// Librería de Laravel para manejar fechas
use App\Models\Subscription;
use App\Models\Plan;

class SubscriptionController extends Controller
{
    //

    // POST: Crear una suscripción (Comprar un plan)
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id', // El usuario debe existir
            'plan_id' => 'required|exists:plans,id', // El plan debe existir
        ]);

        // 1. Buscamos el plan para saber su duración
        $plan = Plan::find($request->plan_id);

        // 2. Calculamos las fechas
        $startDate = Carbon::now(); // Fecha de hoy
        $endDate = $startDate->copy()->addDays($plan->duration_days); // Hoy + duración del plan

        // 3. Creamos la suscripción
        $subscription = Subscription::create([
            'user_id' => $request->user_id,
            'plan_id' => $request->plan_id,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'status' => 'active'
        ]);

        return response()->json([
            'message' => 'Suscripción creada con éxito',
            'data' => $subscription
        ], 201);
    }

    // GET: Ver las suscripciones de un usuario específico
    public function getUserSubscriptions($userId)
    {
        // Traemos las suscripciones CON la información del Plan (usando 'with')
        $subscriptions = Subscription::where('user_id', $userId)
            ->with('plan') // ¡Esto trae el nombre y precio del plan junto con la suscripción!
            ->get();

        return response()->json($subscriptions, 200);
    }

    // PUT: Cancelar suscripción
    public function cancel($id)
    {
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return response()->json(['message' => 'Suscripción no encontrada'], 404);
        }

        $subscription->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Suscripción cancelada'], 200);
    }
}
