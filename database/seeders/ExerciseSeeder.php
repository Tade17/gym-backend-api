<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercise;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        // URLs de videos que permiten reproducción embebida (de canales fitness populares)
        $exercises = [
            // PECHO
            ['name' => 'Press de Banca', 'description' => 'Acostado en banco plano, empujar la barra hacia arriba.', 'muscle_group' => 'Pecho', 'video_url' => 'https://www.youtube.com/watch?v=vcBig73ojpE'],
            ['name' => 'Aperturas con Mancuernas', 'description' => 'Movimiento circular con mancuernas para estirar el pectoral.', 'muscle_group' => 'Pecho', 'video_url' => 'https://www.youtube.com/watch?v=QENKPHhQVi4'],
            ['name' => 'Press Inclinado', 'description' => 'Press en banco inclinado a 30-45 grados.', 'muscle_group' => 'Pecho', 'video_url' => 'https://www.youtube.com/watch?v=IP4oeKh1Sd4'],
            ['name' => 'Fondos en Paralelas', 'description' => 'Flexiones en barras paralelas enfocado en pecho.', 'muscle_group' => 'Pecho', 'video_url' => 'https://www.youtube.com/watch?v=wjUmnZH528Y'],

            // ESPALDA
            ['name' => 'Dominadas', 'description' => 'Tracción vertical colgado de una barra.', 'muscle_group' => 'Espalda', 'video_url' => 'https://www.youtube.com/watch?v=HRV5YKKaeVw'],
            ['name' => 'Remo con Barra', 'description' => 'Remo horizontal con barra para espalda media.', 'muscle_group' => 'Espalda', 'video_url' => 'https://www.youtube.com/watch?v=9efgcAjQe7E'],
            ['name' => 'Peso Muerto', 'description' => 'Levantamiento de peso desde el suelo.', 'muscle_group' => 'Espalda', 'video_url' => 'https://www.youtube.com/watch?v=r4MzxtBKyNE'],
            ['name' => 'Jalón al Pecho', 'description' => 'Tracción en polea alta hacia el pecho.', 'muscle_group' => 'Espalda', 'video_url' => 'https://www.youtube.com/watch?v=SALxEARiMkw'],

            // PIERNAS
            ['name' => 'Sentadilla', 'description' => 'Flexión de rodillas con barra en la espalda.', 'muscle_group' => 'Piernas', 'video_url' => 'https://www.youtube.com/watch?v=Dy28eq2PjcM'],
            ['name' => 'Prensa de Piernas', 'description' => 'Empuje en máquina de prensa inclinada.', 'muscle_group' => 'Piernas', 'video_url' => 'https://www.youtube.com/watch?v=s9-zeWzPUmA'],
            ['name' => 'Extensión de Cuádriceps', 'description' => 'Extensión en máquina para cuádriceps.', 'muscle_group' => 'Piernas', 'video_url' => 'https://www.youtube.com/watch?v=ljO4jkwv8wQ'],
            ['name' => 'Curl Femoral', 'description' => 'Flexión de rodilla en máquina para isquiotibiales.', 'muscle_group' => 'Piernas', 'video_url' => 'https://www.youtube.com/watch?v=ELOCsoDSmrg'],
            ['name' => 'Elevación de Talones', 'description' => 'Elevación de talones para pantorrillas.', 'muscle_group' => 'Piernas', 'video_url' => 'https://www.youtube.com/watch?v=-M4-G8p8fmc'],

            // HOMBROS
            ['name' => 'Press Militar', 'description' => 'Press vertical con barra para deltoides.', 'muscle_group' => 'Hombros', 'video_url' => 'https://www.youtube.com/watch?v=_RlRDWO2jfg'],
            ['name' => 'Elevaciones Laterales', 'description' => 'Elevación de mancuernas hacia los lados.', 'muscle_group' => 'Hombros', 'video_url' => 'https://www.youtube.com/watch?v=3VcKaXpzqRo'],
            ['name' => 'Elevaciones Frontales', 'description' => 'Elevación de mancuernas hacia el frente.', 'muscle_group' => 'Hombros', 'video_url' => 'https://www.youtube.com/watch?v=sOcYlBI85hc'],
            ['name' => 'Pájaros', 'description' => 'Elevaciones posteriores para deltoides trasero.', 'muscle_group' => 'Hombros', 'video_url' => 'https://www.youtube.com/watch?v=lPt0GqwaqEw'],

            // BRAZOS
            ['name' => 'Curl con Barra', 'description' => 'Flexión de codos con barra para bíceps.', 'muscle_group' => 'Bíceps', 'video_url' => 'https://www.youtube.com/watch?v=LY1V6UbRHFM'],
            ['name' => 'Curl Martillo', 'description' => 'Curl con mancuernas en posición neutra.', 'muscle_group' => 'Bíceps', 'video_url' => 'https://www.youtube.com/watch?v=TwD-YGVP4Bk'],
            ['name' => 'Extensión de Tríceps en Polea', 'description' => 'Empujar la barra hacia abajo en polea.', 'muscle_group' => 'Tríceps', 'video_url' => 'https://www.youtube.com/watch?v=REWv05om0ho'],
            ['name' => 'Press Francés', 'description' => 'Extensión de tríceps acostado con barra.', 'muscle_group' => 'Tríceps', 'video_url' => 'https://www.youtube.com/watch?v=d_KZxkY_0cM'],

            // CORE
            ['name' => 'Plancha', 'description' => 'Posición isométrica para core completo.', 'muscle_group' => 'Core', 'video_url' => 'https://www.youtube.com/watch?v=pSHjTRCQxIw'],
            ['name' => 'Crunches', 'description' => 'Flexión abdominal clásica.', 'muscle_group' => 'Core', 'video_url' => 'https://www.youtube.com/watch?v=5ER5Of4MOPI'],
            ['name' => 'Elevación de Piernas', 'description' => 'Elevación de piernas colgado o acostado.', 'muscle_group' => 'Core', 'video_url' => 'https://www.youtube.com/watch?v=Pr1ieGZ5atk'],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create($exercise);
        }
    }
}
