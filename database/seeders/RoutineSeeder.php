<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Routine;
use App\Models\Exercise;
use App\Models\User;

class RoutineSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = User::where('role', 'trainer')->get();

        foreach ($trainers as $trainer) {
            $this->createRoutinesForTrainer($trainer);
        }
    }

    private function createRoutinesForTrainer(User $trainer): void
    {
        // Obtener ejercicios por grupo muscular
        $pecho = Exercise::where('muscle_group', 'Pecho')->pluck('id')->toArray();
        $espalda = Exercise::where('muscle_group', 'Espalda')->pluck('id')->toArray();
        $piernas = Exercise::where('muscle_group', 'Piernas')->pluck('id')->toArray();
        $hombros = Exercise::where('muscle_group', 'Hombros')->pluck('id')->toArray();
        $biceps = Exercise::where('muscle_group', 'Bíceps')->pluck('id')->toArray();
        $triceps = Exercise::where('muscle_group', 'Tríceps')->pluck('id')->toArray();
        $core = Exercise::where('muscle_group', 'Core')->pluck('id')->toArray();

        $routines = [
            [
                'name' => 'Día 1: Pecho y Tríceps',
                'description' => 'Rutina de push para pecho y tríceps.',
                'level' => 'intermediate',
                'estimated_duration' => 60,
                'exercises' => $this->buildExercises(array_merge($pecho, $triceps)),
            ],
            [
                'name' => 'Día 2: Espalda y Bíceps',
                'description' => 'Rutina de pull para espalda y bíceps.',
                'level' => 'intermediate',
                'estimated_duration' => 60,
                'exercises' => $this->buildExercises(array_merge($espalda, $biceps)),
            ],
            [
                'name' => 'Día 3: Piernas Completo',
                'description' => 'Rutina de piernas con cuádriceps, isquios y pantorrillas.',
                'level' => 'intermediate',
                'estimated_duration' => 75,
                'exercises' => $this->buildExercises($piernas),
            ],
            [
                'name' => 'Día 4: Hombros y Core',
                'description' => 'Rutina de deltoides y abdominales.',
                'level' => 'beginner',
                'estimated_duration' => 45,
                'exercises' => $this->buildExercises(array_merge($hombros, $core)),
            ],
            [
                'name' => 'Full Body Principiantes',
                'description' => 'Rutina de cuerpo completo para principiantes.',
                'level' => 'beginner',
                'estimated_duration' => 50,
                'exercises' => $this->buildExercises(array_merge(
                    array_slice($pecho, 0, 1),
                    array_slice($espalda, 0, 1),
                    array_slice($piernas, 0, 2),
                    array_slice($core, 0, 1)
                )),
            ],
            [
                'name' => 'HIIT Quema Grasa',
                'description' => 'Rutina de alta intensidad para pérdida de peso.',
                'level' => 'advanced',
                'estimated_duration' => 30,
                'exercises' => $this->buildExercises(array_merge(
                    array_slice($piernas, 0, 2),
                    $core
                ), true),
            ],
        ];

        foreach ($routines as $routineData) {
            $exercises = $routineData['exercises'];
            unset($routineData['exercises']);

            $routine = Routine::create([
                ...$routineData,
                'trainer_id' => $trainer->id,
            ]);

            // Vincular ejercicios a la rutina
            foreach ($exercises as $exerciseData) {
                $routine->exercises()->attach($exerciseData['id'], [
                    'sets' => $exerciseData['sets'],
                    'reps' => $exerciseData['reps'],
                    'rest_time' => $exerciseData['rest_time'],
                    'notes' => $exerciseData['notes'] ?? null,
                ]);
            }
        }
    }

    private function buildExercises(array $exerciseIds, bool $hiit = false): array
    {
        $exercises = [];
        foreach ($exerciseIds as $id) {
            $exercises[] = [
                'id' => $id,
                'sets' => $hiit ? rand(3, 4) : rand(3, 5),
                'reps' => $hiit ? rand(15, 20) : rand(8, 15),
                'rest_time' => $hiit ? rand(20, 30) : rand(45, 90),
                'notes' => null,
            ];
        }
        return $exercises;
    }
}
