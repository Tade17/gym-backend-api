<?php

namespace App\Console\Commands;

use App\Models\AssignedRoutine;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotifyRetention extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-retention';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notificar a usuarios que faltaron a 3 sesiones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNotNull('fcm_token')->get();

        foreach ($users as $user) {
            // 1. Obtener las últimas 3 rutinas asignadas
            $lastThreeRoutines = AssignedRoutine::where('user_id', $user->id)
                ->orderBy('assigned_date', 'desc')
                ->take(3)
                ->get();

            // Verificar si las últimas 3 están pendientes (status = 0)
            $missedThree = ($lastThreeRoutines->count() == 3 && $lastThreeRoutines->where('status', 0)->count() == 3);

            // 2. Verificar regla de las 2 semanas (si ya se le notificó antes)
            $needsFollowUp = $user->last_retention_notif_at &&
                now()->diffInDays($user->last_retention_notif_at) >= 14;

            if ($missedThree || $needsFollowUp) {
                $this->sendPush($user->fcm_token, '¡Te extrañamos, ' . $user->first_name . '!', 'Vuelve a tus entrenos hoy.');
                $user->update(['last_retention_notif_at' => now()]);
            }
        }
    }

    private function sendPush($token, $title, $body)
    {
        $messaging = app('firebase.messaging');

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body))
            ->withData(['click_action' => 'FLUTTER_NOTIFICATION_CLICK']); 

        try {
            $messaging->send($message);
            Log::info('Notificación enviada con éxito a: ' . $token);
        } catch (\Exception $e) {
            Log::error('Error enviando PUSH: ' . $e->getMessage());
        }
    }
}
