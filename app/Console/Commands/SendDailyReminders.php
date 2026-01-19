<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DietPlanMeal;
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
    $today = $now->toDateString();

    // 1. Recordatorio de Nutrición (Avisa 15 min antes de la hora sugerida)
    $meals = DietPlanMeal::whereHas('dietPlan', function($q) use ($today) {
                $q->whereHas('assignedDiets', function($sq) use ($today) {
                    $sq->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today);
                });
            })
            ->whereTime('suggested_time', '>=', $now->toTimeString())
            ->whereTime('suggested_time', '<=', $now->addMinutes(30)->toTimeString())
            ->get();

    foreach ($meals as $meal) {
        // Lógica de envío al token del usuario vinculado...
    }
    }
}
