<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Enviar alerta por WhatsApp a clientes con suscripción próxima a vencer
     * POST /api/notifications/expiring-alert
     */
    public function sendExpiringAlert(Request $request)
    {
        $request->validate([
            'client_ids' => 'required|array',
            'client_ids.*' => 'exists:users,id'
        ]);

        // Verificar conexión con WAHA
        if (!$this->whatsAppService->isConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'WAHA no está conectado. Verifica que el servicio esté activo.'
            ], 503);
        }

        // Obtener los clientes con sus suscripciones
        $clients = User::whereIn('id', $request->client_ids)
            ->with([
                'subscriptions' => function ($query) {
                    $query->latest()->first();
                },
                'subscriptions.plan'
            ])
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name,
                    'phone_number' => $client->phone_number,
                    'email' => $client->email,
                    'subscription' => $client->subscriptions->first(),
                ];
            })
            ->toArray();

        // Enviar mensajes masivos
        $results = $this->whatsAppService->sendBulkMessages($clients, function ($client) {
            $sub = $client['subscription'] ?? null;
            $planName = $sub['plan']['name'] ?? 'tu plan';
            $endDate = $sub ? Carbon::parse($sub['end_date'])->format('d/m/Y') : 'pronto';

            // Calcular días restantes correctamente (número entero)
            $daysLeft = 0;
            if ($sub && $sub['end_date']) {
                $endDateCarbon = Carbon::parse($sub['end_date'])->startOfDay();
                $today = Carbon::now()->startOfDay();
                $daysLeft = (int) $today->diffInDays($endDateCarbon, false); // false = puede ser negativo
            }

            // Construir mensaje según los días restantes
            $daysMessage = $daysLeft > 0
                ? "quedan *{$daysLeft} días*"
                : ($daysLeft === 0 ? "vence *hoy*" : "venció hace " . abs($daysLeft) . " días");

            return "¡Hola {$client['first_name']}! 👋\n\n" .
                "Te recordamos que tu suscripción de *{$planName}* vence el *{$endDate}* " .
                "({$daysMessage}).\n\n" .
                "🏋️ ¡No pierdas tu ritmo de entrenamiento!\n" .
                "Renueva tu plan y sigue alcanzando tus metas.\n\n" .
                "Para más información, contáctanos. 💪";
        });

        return response()->json([
            'success' => true,
            'message' => "Alertas enviadas: {$results['sent']} de {$results['total']}",
            'results' => $results
        ]);
    }

    /**
     * Verificar estado de conexión con WAHA
     * GET /api/notifications/status
     */
    public function checkStatus()
    {
        $isConnected = $this->whatsAppService->isConnected();

        return response()->json([
            'connected' => $isConnected,
            'message' => $isConnected
                ? 'WAHA conectado y listo para enviar mensajes'
                : 'WAHA no está conectado'
        ]);
    }
}
