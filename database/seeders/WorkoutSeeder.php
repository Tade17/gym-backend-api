<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Exercise;
use App\Models\Routine;
use App\Models\User;
use App\Models\Plan;

class WorkoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //// 1. Crear biblioteca de ejercicios básicos
        $ex1 = Exercise::create([
            'name' => 'Press de Banca',
            'description' => 'Acostado en banco plano, empujar la barra hacia arriba.',
            'muscle_group' => 'Pecho',
            'video_url' => 'https://youtube.com/watch?v=ejercicio1'
        ]);

        $ex2 = Exercise::create([
            'name' => 'Aperturas con Mancuernas',
            'description' => 'Movimiento circular con mancuernas para estirar el pectoral.',
            'muscle_group' => 'Pecho',
            'video_url' => 'https://youtube.com/watch?v=ejercicio2'
        ]);

        $ex3 = Exercise::create([
            'name' => 'Extensión de Tríceps en Polea',
            'description' => 'Empujar la barra hacia abajo manteniendo los codos fijos.',
            'muscle_group' => 'Tríceps',
            'video_url' => 'https://youtube.com/watch?v=ejercicio3'
        ]);

        // 2. Obtener al entrenador y el plan que creamos antes
        $trainer = User::where('role', 'trainer')->first();
        $plan = Plan::where('type', 'basic')->first();

        if ($trainer && $plan) {
            // 3. Crear una Rutina de ejemplo
            $routine = Routine::create([
                'name' => 'Lunes: Pecho y Tríceps Explosivo',
                'description' => 'Rutina enfocada en fuerza e hipertrofia para principiantes.',
                'level' => 'beginner',
                'estimated_duration' => 60,
                'trainer_id' => $trainer->id,
                'plan_id' => $plan->id // Vinculada al Plan Básico
            ]);

            // 4. Vincular los ejercicios a la rutina usando la tabla PIVOTE con datos extra
            $routine->exercises()->attach([
                $ex1->id => ['sets' => 4, 'reps' => 12, 'rest_time' => 90],
                $ex2->id => ['sets' => 3, 'reps' => 15, 'rest_time' => 60],
                $ex3->id => ['sets' => 4, 'reps' => 12, 'rest_time' => 45],
            ]);
        }
    }
}
