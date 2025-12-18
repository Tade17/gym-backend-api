<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Plan;

class TrainerWithPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear el Entrenador de prueba
        $trainer = User::create([
            'first_name' => 'Diego',
            'last_name' => 'Robles',
            'email' => 'diego@trainer.com',
            'password' => Hash::make('password123'),
            'role' => 'trainer',
            'weight' => 85.0,
            'height' => 1.80,
            'birth_date' => '1995-05-10',
        ]);

        // 2. Crear los 3 tipos de planes para este entrenador
        Plan::create([
            'type' => 'basic',
            'description' => 'Acceso solo a rutinas básicas.',
            'price' => 50.00,
            'duration_days' => 30,
            'trainer_id' => $trainer->id,
        ]);

        Plan::create([
            'type' => 'pro',
            'description' => 'Incluye rutinas avanzadas y guía de nutrición.',
            'price' => 120.00,
            'duration_days' => 30,
            'trainer_id' => $trainer->id,
        ]);

        Plan::create([
            'type' => 'personalized',
            'description' => 'Seguimiento 1 a 1 y chat directo.',
            'price' => 250.00,
            'duration_days' => 30,
            'trainer_id' => $trainer->id,
        ]);
    }
}
