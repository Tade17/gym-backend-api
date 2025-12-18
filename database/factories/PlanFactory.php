<?php
//Este archivo es para crear datos falsos de planes en la base de datos para pruebas
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Eloquent inventará datos por nosotros
            'type' => fake()->randomElement(['basic', 'pro', 'personalized']),
            'description' => fake()->sentence(), // Una oración falsa
            'price' => fake()->randomFloat(2, 50, 200), // Precio entre 50.00 y 200.00
            'duration_days' => fake()->randomElement([30, 90, 365]), // Elige uno de estos
            'is_active' => true,
            'trainer_id' => User::factory()->create(['role' => 'trainer'])->id,
        ];
    }
}
