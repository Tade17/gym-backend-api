<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Usuarios Base (Admin y Entrenadores)
        User::factory()->create([
            'first_name' => 'Tadeo',
            'last_name' => 'Mendoza Gastulo',
            'email' => 'tadeomendoza@gym.com',
            'role' => 'admin',
        ]);

        // 2. Crear al Entrenador "Diego" primero
        $this->call(TrainerWithPlansSeeder::class);

        // 3. CREAR A WILSON AQUÍ (Antes de los seeders de contenido)
        User::factory()->client()->create([
            'first_name' => 'Wilson',
            'email' => 'wilson@test.com',
            'password' => bcrypt('wilson123'),
            'assigned_trainer_id' => User::where('email', 'diego@trainer.com')->first()->id
        ]);

        // 4. Crear otros usuarios aleatorios
        User::factory()->trainer()->count(3)->create();
        User::factory()->client()->count(7)->create();

        // 5. Lógica de Planes (Asegurar que todos tengan planes)
        $trainers = User::where('role', 'trainer')->get();
        foreach ($trainers as $trainer) {
            foreach (['basic', 'pro', 'personalized'] as $type) {
                if (!Plan::where('trainer_id', $trainer->id)->where('type', $type)->exists()) {
                    Plan::create([
                        'type' => $type,
                        'description' => "Plan $type de {$trainer->first_name}.",
                        'price' => ($type == 'basic') ? 50 : 100,
                        'duration_days' => 30,
                        'trainer_id' => $trainer->id,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // 6. Ahora sí, llamar a los seeders que ASIGNAN cosas a Wilson
        $this->call([
            MealSeeder::class,
            WorkoutSeeder::class,
            NutritionSeeder::class, // Este ya encontrará a Wilson
        ]);
    }
}
