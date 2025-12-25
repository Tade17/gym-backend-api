<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Meal;

class MealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $comidas = [
            ['name' => 'Omelette de Claras', 'description' => '4 claras de huevo con espinaca.'],
            ['name' => 'Batido Post-Entreno', 'description' => '1 scoop de proteína con agua y canela.'],
            ['name' => 'Salmón a la Plancha', 'description' => '200g de salmón con espárragos.'],
            ['name' => 'Yogurt Griego', 'description' => 'Yogurt sin azúcar con 5 almendras.']
        ];

        foreach ($comidas as $comida) {
            Meal::create($comida);
        }
    }
}
