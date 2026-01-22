<?php

namespace App\Console\Commands;

use App\Models\DietPlanMeal;
use Illuminate\Console\Command;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;
class SendDailyReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'avisar a los usuarios sus comidas del dia con 15 minutos de anticipacion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $today = $now->toDateString();
        $timeWindow = $now->copy()->addMinutes(30)->toTimeString();
        $currentTime = $now->toTimeString();

        // 1. Recordatorio de Nutrición (Avisa 15 min antes de la hora sugerida)
        $meals = DietPlanMeal::whereHas('dietPlan', function ($q) use ($today) {
            $q->whereHas('assignedDiets', function ($sq) use ($today) {
                $sq->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today);
            });
        })
            ->whereTime('suggested_time', '>=', $currentTime)
            ->whereTime('suggested_time', '<=', $timeWindow)
            ->get();

        foreach ($meals as $meal) {
            $user = $meal->dietPlan->user;
            if ($user && $user->fcm_token) {
                $this->sendPush($user->fcm_token, 'Meal Reminder', "Time for: {$meal->name}");
            }
        }
    }

    private function sendPush($token, $title, $body)
    {
        $messaging = app('firebase.messaging');

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body));
        try {
            $messaging->send($message);
            Log::info('Notificación enviada con éxito a: ' . $token);
        } catch (\Exception $e) {
            Log::error('Error enviando PUSH: ' . $e->getMessage());
        }
    }
}

