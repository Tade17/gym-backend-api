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
        // User::factory(10)->create();

        // 1. Crear un usuario ADMIN real (para que tú puedas hacer login)
        User::factory()->create([
            'first_name' => 'Tadeo',
            'last_name' => 'Mendoza Gastulo',
            'email' => 'tadeomendoza@gym.com',
            'password' => bcrypt('tadeo123'), // Eloquent encripta solo
            'role' => 'admin',
            'weight' => 74.4,
            'height' => 1.70,
            'birth_date' => '2004-05-17'
        ]);
        
        // 2. Crear 10 usuarios "Dummy" (falsos) aleatorios
        User::factory(10)->create();

        // 3. Crear 5 Planes falsos usando la fábrica que hicimos
        Plan::factory(5)->create();
    }
}
