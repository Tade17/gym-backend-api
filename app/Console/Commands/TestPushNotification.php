<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class TestPushNotification extends Command
{
    protected $signature = 'app:test-push {user_id?}';
    protected $description = 'Enviar una notificación de prueba a un usuario';

    public function handle()
    {
        $userId = $this->argument('user_id');

        if ($userId) {
            $user = User::find($userId);
        } else {
            // Obtener el primer usuario con token FCM
            $user = User::whereNotNull('fcm_token')->first();
        }

        if (!$user || !$user->fcm_token) {
            $this->error('No se encontró usuario con FCM token');
            return;
        }

        $this->info("Enviando notificación a: {$user->first_name} ({$user->email})");
        $this->info("Token: " . substr($user->fcm_token, 0, 30) . "...");

        try {
            $messaging = app('firebase.messaging');

            $message = CloudMessage::withTarget('token', $user->fcm_token)
                ->withNotification(Notification::create(
                    '¡Prueba exitosa! 🎉',
                    'Las notificaciones push están funcionando correctamente.'
                ))
                ->withData(['click_action' => 'FLUTTER_NOTIFICATION_CLICK']);

            $messaging->send($message);

            $this->info('✅ Notificación enviada con éxito!');
            Log::info('Notificación de prueba enviada a: ' . $user->email);

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            Log::error('Error en prueba de push: ' . $e->getMessage());
        }
    }
}
