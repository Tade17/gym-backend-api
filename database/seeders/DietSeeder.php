<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DietPlan;
use App\Models\Meal;
use App\Models\Food;
use App\Models\User;

class DietSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = User::where('role', 'trainer')->get();

        // Primero crear las comidas base
        $this->createMeals();

        // Luego crear planes de dieta para cada entrenador
        foreach ($trainers as $trainer) {
            $this->createDietPlansForTrainer($trainer);
        }
    }

    private function createMeals(): void
    {
        $proteinas = Food::where('category', 'Proteína')->pluck('id')->toArray();
        $carbos = Food::where('category', 'Cereal/Grano')->pluck('id')->toArray();
        $verduras = Food::where('category', 'Verdura')->pluck('id')->toArray();
        $frutas = Food::where('category', 'Fruta')->pluck('id')->toArray();
        $lacteos = Food::where('category', 'Lácteo')->pluck('id')->toArray();

        $meals = [
            [
                'name' => 'Desayuno Proteico',
                'description' => 'Desayuno alto en proteínas para comenzar el día.',
                'foods' => [
                    ['id' => $proteinas[4] ?? 1, 'quantity' => '3 unidades'], // Huevo
                    ['id' => $carbos[2] ?? 1, 'quantity' => '50g'], // Avena
                    ['id' => $frutas[0] ?? 1, 'quantity' => '1 unidad'], // Plátano
                ],
            ],
            [
                'name' => 'Almuerzo Balanceado',
                'description' => 'Almuerzo equilibrado con proteína, carbohidrato y vegetales.',
                'foods' => [
                    ['id' => $proteinas[0] ?? 1, 'quantity' => '200g'], // Pollo
                    ['id' => $carbos[0] ?? 1, 'quantity' => '150g'], // Arroz integral
                    ['id' => $verduras[0] ?? 1, 'quantity' => '100g'], // Brócoli
                    ['id' => $verduras[3] ?? 1, 'quantity' => '50g'], // Zanahoria
                ],
            ],
            [
                'name' => 'Merienda Post-Entreno',
                'description' => 'Snack ideal para después del entrenamiento.',
                'foods' => [
                    ['id' => $lacteos[0] ?? 1, 'quantity' => '200g'], // Yogurt griego
                    ['id' => $frutas[4] ?? 1, 'quantity' => '50g'], // Arándanos
                ],
            ],
            [
                'name' => 'Cena Liviana',
                'description' => 'Cena baja en carbohidratos.',
                'foods' => [
                    ['id' => $proteinas[2] ?? 1, 'quantity' => '180g'], // Salmón
                    ['id' => $verduras[2] ?? 1, 'quantity' => '100g'], // Espárragos
                    ['id' => $verduras[1] ?? 1, 'quantity' => '50g'], // Espinaca
                ],
            ],
            [
                'name' => 'Snack Energético',
                'description' => 'Snack para media mañana o tarde.',
                'foods' => [
                    ['id' => $frutas[1] ?? 1, 'quantity' => '1 unidad'], // Manzana
                    ['id' => Food::where('category', 'Grasa')->first()->id ?? 1, 'quantity' => '20g'], // Almendras
                ],
            ],
            [
                'name' => 'Batido de Proteína',
                'description' => 'Batido rápido post-entreno.',
                'foods' => [
                    ['id' => $lacteos[1] ?? 1, 'quantity' => '300ml'], // Leche
                    ['id' => $frutas[0] ?? 1, 'quantity' => '1 unidad'], // Plátano
                ],
            ],
        ];

        foreach ($meals as $mealData) {
            $foods = $mealData['foods'];
            unset($mealData['foods']);

            $meal = Meal::create($mealData);

            foreach ($foods as $foodData) {
                $meal->food()->attach($foodData['id'], [
                    'quantity' => $foodData['quantity'],
                ]);
            }
        }
    }

    private function createDietPlansForTrainer(User $trainer): void
    {
        $meals = Meal::all();

        $dietPlans = [
            [
                'name' => 'Dieta Hipertrofia',
                'goal' => 'Ganancia Muscular',
                'description' => 'Plan alimenticio para ganar masa muscular.',
            ],
            [
                'name' => 'Dieta Definición',
                'goal' => 'Pérdida de Peso',
                'description' => 'Plan bajo en calorías para definir.',
            ],
            [
                'name' => 'Dieta Mantenimiento',
                'goal' => 'Tonificación',
                'description' => 'Plan equilibrado para mantener peso y tonificar.',
            ],
        ];

        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $mealTypes = ['breakfast', 'snack', 'lunch', 'snack', 'dinner'];
        $times = ['07:00:00', '10:00:00', '13:00:00', '16:00:00', '20:00:00'];

        foreach ($dietPlans as $planData) {
            $dietPlan = DietPlan::create([
                ...$planData,
                'trainer_id' => $trainer->id,
            ]);

            // Asignar comidas para cada día de la semana
            foreach ($daysOfWeek as $day) {
                // Asignar 3-5 comidas por día
                $mealsForDay = $meals->random(rand(3, 5));
                $mealIndex = 0;

                foreach ($mealsForDay as $meal) {
                    $dietPlan->meals()->attach($meal->id, [
                        'day_of_week' => $day,
                        'meal_type' => $mealTypes[$mealIndex % 5],
                        'suggested_time' => $times[$mealIndex % 5],
                    ]);
                    $mealIndex++;
                }
            }
        }
    }
}
