<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. ADMIN
        // ==========================================
        User::create([
            'first_name' => 'Tadeo',
            'last_name' => 'Admin',
            'email' => 'admin@gym.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'weight' => 75.0,
            'height' => 1.75,
            'birth_date' => '1990-01-15',
        ]);

        // ==========================================
        // 2. ENTRENADORES (sin evento booted para evitar duplicados)
        // ==========================================
        $trainers = [
            [
                'first_name' => 'Diego',
                'last_name' => 'Robles',
                'email' => 'diego@trainer.com',
                'password' => Hash::make('password123'),
                'weight' => 85.0,
                'height' => 1.80,
                'birth_date' => '1995-05-10',
            ],
            [
                'first_name' => 'María',
                'last_name' => 'García',
                'email' => 'maria@trainer.com',
                'password' => Hash::make('password123'),
                'weight' => 60.0,
                'height' => 1.65,
                'birth_date' => '1992-08-22',
            ],
        ];

        foreach ($trainers as $trainerData) {
            // Crear trainer SIN disparar evento booted
            $trainer = new User($trainerData);
            $trainer->role = 'trainer';
            $trainer->saveQuietly(); // No dispara eventos

            // Crear planes manualmente para cada entrenador
            $this->createPlansForTrainer($trainer);
        }

        // ==========================================
        // 3. CLIENTES DE PRUEBA
        // ==========================================
        $diego = User::where('email', 'diego@trainer.com')->first();
        $maria = User::where('email', 'maria@trainer.com')->first();

        $clients = [
            // Clientes de Diego
            ['first_name' => 'Wilson', 'last_name' => 'Test', 'email' => 'wilson@test.com', 'trainer' => $diego, 'goal' => 'Ganancia Muscular'],
            ['first_name' => 'Carlos', 'last_name' => 'Pérez', 'email' => 'carlos@test.com', 'trainer' => $diego, 'goal' => 'Pérdida de Peso'],
            ['first_name' => 'Ana', 'last_name' => 'López', 'email' => 'ana@test.com', 'trainer' => $diego, 'goal' => 'Ganancia Muscular'],
            ['first_name' => 'Luis', 'last_name' => 'Martínez', 'email' => 'luis@test.com', 'trainer' => $diego, 'goal' => 'Tonificación'],
            ['first_name' => 'Sofía', 'last_name' => 'Hernández', 'email' => 'sofia@test.com', 'trainer' => $diego, 'goal' => 'Pérdida de Peso'],

            // Clientes de María
            ['first_name' => 'Pedro', 'last_name' => 'Sánchez', 'email' => 'pedro@test.com', 'trainer' => $maria, 'goal' => 'Ganancia Muscular'],
            ['first_name' => 'Laura', 'last_name' => 'Rodríguez', 'email' => 'laura@test.com', 'trainer' => $maria, 'goal' => 'Tonificación'],
            ['first_name' => 'Miguel', 'last_name' => 'Torres', 'email' => 'miguel@test.com', 'trainer' => $maria, 'goal' => 'Pérdida de Peso'],
        ];

        foreach ($clients as $clientData) {
            $trainer = $clientData['trainer'];
            $goal = $clientData['goal'];
            unset($clientData['trainer'], $clientData['goal']);

            $client = User::create([
                ...$clientData,
                'password' => Hash::make('client123'),
                'role' => 'client',
                'weight' => rand(55, 95) + (rand(0, 9) / 10),
                'height' => 1.5 + (rand(15, 40) / 100),
                'birth_date' => Carbon::now()->subYears(rand(20, 45))->format('Y-m-d'),
                'goals' => $goal,
                'assigned_trainer_id' => $trainer->id,
            ]);

            // Suscribir al plan Pro del entrenador
            $proPlan = Plan::where('trainer_id', $trainer->id)
                ->where('type', 'pro')
                ->first();

            if ($proPlan) {
                Subscription::create([
                    'user_id' => $client->id,
                    'plan_id' => $proPlan->id,
                    'start_date' => Carbon::now()->subDays(rand(1, 15)),
                    'end_date' => Carbon::now()->addDays(rand(15, 30)),
                    'status' => 1, // Activa
                ]);
            }
        }
    }

    private function createPlansForTrainer(User $trainer): void
    {
        $plans = [
            [
                'name' => 'Plan Básico',
                'type' => 'basic',
                'description' => 'Acceso a rutinas básicas de entrenamiento.',
                'price' => 50.00,
                'duration_days' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Plan Pro',
                'type' => 'pro',
                'description' => 'Rutinas avanzadas + guía de nutrición personalizada.',
                'price' => 120.00,
                'duration_days' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Plan Personalizado',
                'type' => 'personalized',
                'description' => 'Seguimiento 1 a 1, chat directo y rutinas a medida.',
                'price' => 250.00,
                'duration_days' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::create([
                ...$planData,
                'trainer_id' => $trainer->id,
            ]);
        }
    }
}
