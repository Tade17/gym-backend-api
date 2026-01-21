<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Orden de ejecución:
     * 1. ExerciseSeeder - Biblioteca de ejercicios base
     * 2. FoodSeeder - Biblioteca de alimentos base
     * 3. UserSeeder - Admin, entrenadores y clientes
     * 4. RoutineSeeder - Rutinas con ejercicios
     * 5. DietSeeder - Planes de dieta con comidas
     * 6. AssignmentSeeder - Asignar rutinas y dietas a clientes
     */
    public function run(): void
    {
        $this->call([
            ExerciseSeeder::class,
            FoodSeeder::class,
            UserSeeder::class,
            RoutineSeeder::class,
            DietSeeder::class,
            AssignmentSeeder::class,
        ]);
    }
}
