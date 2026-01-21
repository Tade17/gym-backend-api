<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Routine;
use App\Models\DietPlan;
use App\Models\AssignedRoutine;
use App\Models\AssignedDiet;
use Carbon\Carbon;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::where('role', 'client')->get();

        foreach ($clients as $client) {
            $this->assignRoutinesToClient($client);
            $this->assignDietToClient($client);
        }
    }

    private function assignRoutinesToClient(User $client): void
    {
        $trainerId = $client->assigned_trainer_id;

        if (!$trainerId)
            return;

        $routines = Routine::where('trainer_id', $trainerId)->get();

        if ($routines->isEmpty())
            return;

        // Asignar rutinas para los próximos 7 días
        for ($i = 0; $i < 7; $i++) {
            $routine = $routines->random();
            $date = Carbon::today()->addDays($i);

            // No duplicar si ya existe
            $exists = AssignedRoutine::where('user_id', $client->id)
                ->where('assigned_date', $date->format('Y-m-d'))
                ->exists();

            if (!$exists) {
                AssignedRoutine::create([
                    'user_id' => $client->id,
                    'routine_id' => $routine->id,
                    'trainer_id' => $trainerId,
                    'assigned_date' => $date->format('Y-m-d'),
                    'status' => $i < 2 ? 1 : 0, // Primeros 2 días completados
                    'rating' => $i < 2 ? rand(3, 5) : null,
                ]);
            }
        }

        // También asignar rutinas de días pasados (historial)
        for ($i = 1; $i <= 5; $i++) {
            $routine = $routines->random();
            $date = Carbon::today()->subDays($i);

            $exists = AssignedRoutine::where('user_id', $client->id)
                ->where('assigned_date', $date->format('Y-m-d'))
                ->exists();

            if (!$exists) {
                AssignedRoutine::create([
                    'user_id' => $client->id,
                    'routine_id' => $routine->id,
                    'trainer_id' => $trainerId,
                    'assigned_date' => $date->format('Y-m-d'),
                    'status' => rand(0, 1), // Algunos completados, otros no
                    'rating' => rand(0, 1) ? rand(3, 5) : null,
                ]);
            }
        }
    }

    private function assignDietToClient(User $client): void
    {
        $trainerId = $client->assigned_trainer_id;

        if (!$trainerId)
            return;

        // Buscar un plan de dieta que coincida con el objetivo del cliente
        $dietPlan = DietPlan::where('trainer_id', $trainerId)
            ->where('goal', 'LIKE', '%' . ($client->goals ?? 'Ganancia') . '%')
            ->first();

        // Si no hay coincidencia, tomar el primero disponible
        if (!$dietPlan) {
            $dietPlan = DietPlan::where('trainer_id', $trainerId)->first();
        }

        if (!$dietPlan)
            return;

        // Verificar si ya tiene asignación
        $exists = AssignedDiet::where('user_id', $client->id)
            ->where('diet_plan_id', $dietPlan->id)
            ->exists();

        if (!$exists) {
            AssignedDiet::create([
                'user_id' => $client->id,
                'diet_plan_id' => $dietPlan->id,
                'trainer_id' => $trainerId,
                'start_date' => Carbon::today()->subDays(5),
                'end_date' => Carbon::today()->addDays(25),
            ]);
        }
    }
}
