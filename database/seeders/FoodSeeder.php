<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Food;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        $foods = [
            // PROTEÍNAS
            ['name' => 'Pechuga de Pollo', 'category' => 'Proteína', 'calories_per_100g' => 165],
            ['name' => 'Pechuga de Pavo', 'category' => 'Proteína', 'calories_per_100g' => 135],
            ['name' => 'Salmón', 'category' => 'Proteína', 'calories_per_100g' => 208],
            ['name' => 'Atún en Agua', 'category' => 'Proteína', 'calories_per_100g' => 116],
            ['name' => 'Huevo Entero', 'category' => 'Proteína', 'calories_per_100g' => 155],
            ['name' => 'Claras de Huevo', 'category' => 'Proteína', 'calories_per_100g' => 52],
            ['name' => 'Carne de Res Magra', 'category' => 'Proteína', 'calories_per_100g' => 250],
            ['name' => 'Lomo de Cerdo', 'category' => 'Proteína', 'calories_per_100g' => 143],
            ['name' => 'Tofu', 'category' => 'Proteína', 'calories_per_100g' => 76],
            ['name' => 'Camarones', 'category' => 'Proteína', 'calories_per_100g' => 99],

            // CARBOHIDRATOS
            ['name' => 'Arroz Integral', 'category' => 'Cereal/Grano', 'calories_per_100g' => 111],
            ['name' => 'Arroz Blanco', 'category' => 'Cereal/Grano', 'calories_per_100g' => 130],
            ['name' => 'Avena', 'category' => 'Cereal/Grano', 'calories_per_100g' => 389],
            ['name' => 'Quinoa', 'category' => 'Cereal/Grano', 'calories_per_100g' => 120],
            ['name' => 'Papa', 'category' => 'Cereal/Grano', 'calories_per_100g' => 77],
            ['name' => 'Camote', 'category' => 'Cereal/Grano', 'calories_per_100g' => 86],
            ['name' => 'Pan Integral', 'category' => 'Cereal/Grano', 'calories_per_100g' => 247],
            ['name' => 'Pasta Integral', 'category' => 'Cereal/Grano', 'calories_per_100g' => 124],

            // VERDURAS
            ['name' => 'Brócoli', 'category' => 'Verdura', 'calories_per_100g' => 34],
            ['name' => 'Espinaca', 'category' => 'Verdura', 'calories_per_100g' => 23],
            ['name' => 'Espárragos', 'category' => 'Verdura', 'calories_per_100g' => 20],
            ['name' => 'Zanahoria', 'category' => 'Verdura', 'calories_per_100g' => 41],
            ['name' => 'Tomate', 'category' => 'Verdura', 'calories_per_100g' => 18],
            ['name' => 'Pepino', 'category' => 'Verdura', 'calories_per_100g' => 16],
            ['name' => 'Lechuga', 'category' => 'Verdura', 'calories_per_100g' => 15],
            ['name' => 'Pimiento', 'category' => 'Verdura', 'calories_per_100g' => 31],

            // FRUTAS
            ['name' => 'Plátano', 'category' => 'Fruta', 'calories_per_100g' => 89],
            ['name' => 'Manzana', 'category' => 'Fruta', 'calories_per_100g' => 52],
            ['name' => 'Naranja', 'category' => 'Fruta', 'calories_per_100g' => 47],
            ['name' => 'Fresas', 'category' => 'Fruta', 'calories_per_100g' => 32],
            ['name' => 'Arándanos', 'category' => 'Fruta', 'calories_per_100g' => 57],
            ['name' => 'Piña', 'category' => 'Fruta', 'calories_per_100g' => 50],

            // LÁCTEOS
            ['name' => 'Yogurt Griego', 'category' => 'Lácteo', 'calories_per_100g' => 59],
            ['name' => 'Leche Descremada', 'category' => 'Lácteo', 'calories_per_100g' => 34],
            ['name' => 'Queso Cottage', 'category' => 'Lácteo', 'calories_per_100g' => 98],
            ['name' => 'Queso Panela', 'category' => 'Lácteo', 'calories_per_100g' => 206],

            // GRASAS SALUDABLES
            ['name' => 'Aguacate', 'category' => 'Grasa', 'calories_per_100g' => 160],
            ['name' => 'Almendras', 'category' => 'Grasa', 'calories_per_100g' => 579],
            ['name' => 'Nueces', 'category' => 'Grasa', 'calories_per_100g' => 654],
            ['name' => 'Aceite de Oliva', 'category' => 'Grasa', 'calories_per_100g' => 884],
            ['name' => 'Mantequilla de Maní', 'category' => 'Grasa', 'calories_per_100g' => 588],
        ];

        foreach ($foods as $food) {
            Food::create($food);
        }
    }
}
