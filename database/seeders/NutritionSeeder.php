<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DietPlan;
use App\Models\Meal;
use App\Models\User;
use App\Models\Plan;
use App\Models\AssignedDiet;


class NutritionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trainer = User::where('email', 'diego@trainer.com')->first();
        $planPro = Plan::where('trainer_id', $trainer->id)->where('type', 'pro')->first();

        // Buscar al Wilson que creamos en DatabaseSeeder
        $wilson = User::where('email', 'wilson@test.com')->first();

        // Verificación de seguridad para evitar el error "on null"
        if (!$wilson) {
            $this->command->error("No se encontró al usuario Wilson. Revisa el DatabaseSeeder.");
            return;
        }

        $diet = DietPlan::create([
            'name' => 'Dieta Hipertrofia Pro',
            'goal' => 'Ganancia Muscular',
            'trainer_id' => $trainer->id,
            'plan_id' => $planPro->id
        ]);

        // Vincular comidas
        $meal = Meal::first();
        if ($meal) {
            $diet->meals()->attach($meal->id, [
                'suggested_time' => '08:00:00',
                'meal_type' => 'breakfast',
                'day_of_week' => now()->format('Y-m-d')
            ]);
        }

        // Asignar
        AssignedDiet::create([
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'diet_plan_id' => $diet->id,
            'user_id' => $wilson->id
        ]);
    }
}
