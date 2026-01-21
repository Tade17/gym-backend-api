<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercise;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            // PECHO
            ['name' => 'Press de Banca', 'description' => 'Acostado en banco plano, empujar la barra hacia arriba.', 'muscle_group' => 'Pecho', 'video_url' => 'https://youtube.com/watch?v=press-banca'],
            ['name' => 'Aperturas con Mancuernas', 'description' => 'Movimiento circular con mancuernas para estirar el pectoral.', 'muscle_group' => 'Pecho', 'video_url' => 'https://youtube.com/watch?v=aperturas'],
            ['name' => 'Press Inclinado', 'description' => 'Press en banco inclinado a 30-45 grados.', 'muscle_group' => 'Pecho', 'video_url' => 'https://youtube.com/watch?v=press-inclinado'],
            ['name' => 'Fondos en Paralelas', 'description' => 'Flexiones en barras paralelas enfocado en pecho.', 'muscle_group' => 'Pecho', 'video_url' => 'https://youtube.com/watch?v=fondos'],

            // ESPALDA
            ['name' => 'Dominadas', 'description' => 'Tracción vertical colgado de una barra.', 'muscle_group' => 'Espalda', 'video_url' => 'https://youtube.com/watch?v=dominadas'],
            ['name' => 'Remo con Barra', 'description' => 'Remo horizontal con barra para espalda media.', 'muscle_group' => 'Espalda', 'video_url' => 'https://youtube.com/watch?v=remo-barra'],
            ['name' => 'Peso Muerto', 'description' => 'Levantamiento de peso desde el suelo.', 'muscle_group' => 'Espalda', 'video_url' => 'https://youtube.com/watch?v=peso-muerto'],
            ['name' => 'Jalón al Pecho', 'description' => 'Tracción en polea alta hacia el pecho.', 'muscle_group' => 'Espalda', 'video_url' => 'https://youtube.com/watch?v=jalon-pecho'],

            // PIERNAS
            ['name' => 'Sentadilla', 'description' => 'Flexión de rodillas con barra en la espalda.', 'muscle_group' => 'Piernas', 'video_url' => 'https://youtube.com/watch?v=sentadilla'],
            ['name' => 'Prensa de Piernas', 'description' => 'Empuje en máquina de prensa inclinada.', 'muscle_group' => 'Piernas', 'video_url' => 'https://youtube.com/watch?v=prensa'],
            ['name' => 'Extensión de Cuádriceps', 'description' => 'Extensión en máquina para cuádriceps.', 'muscle_group' => 'Piernas', 'video_url' => 'https://youtube.com/watch?v=extension-cuadriceps'],
            ['name' => 'Curl Femoral', 'description' => 'Flexión de rodilla en máquina para isquiotibiales.', 'muscle_group' => 'Piernas', 'video_url' => 'https://youtube.com/watch?v=curl-femoral'],
            ['name' => 'Elevación de Talones', 'description' => 'Elevación de talones para pantorrillas.', 'muscle_group' => 'Piernas', 'video_url' => 'https://youtube.com/watch?v=elevacion-talones'],

            // HOMBROS
            ['name' => 'Press Militar', 'description' => 'Press vertical con barra para deltoides.', 'muscle_group' => 'Hombros', 'video_url' => 'https://youtube.com/watch?v=press-militar'],
            ['name' => 'Elevaciones Laterales', 'description' => 'Elevación de mancuernas hacia los lados.', 'muscle_group' => 'Hombros', 'video_url' => 'https://youtube.com/watch?v=laterales'],
            ['name' => 'Elevaciones Frontales', 'description' => 'Elevación de mancuernas hacia el frente.', 'muscle_group' => 'Hombros', 'video_url' => 'https://youtube.com/watch?v=frontales'],
            ['name' => 'Pájaros', 'description' => 'Elevaciones posteriores para deltoides trasero.', 'muscle_group' => 'Hombros', 'video_url' => 'https://youtube.com/watch?v=pajaros'],

            // BRAZOS
            ['name' => 'Curl con Barra', 'description' => 'Flexión de codos con barra para bíceps.', 'muscle_group' => 'Bíceps', 'video_url' => 'https://youtube.com/watch?v=curl-barra'],
            ['name' => 'Curl Martillo', 'description' => 'Curl con mancuernas en posición neutra.', 'muscle_group' => 'Bíceps', 'video_url' => 'https://youtube.com/watch?v=curl-martillo'],
            ['name' => 'Extensión de Tríceps en Polea', 'description' => 'Empujar la barra hacia abajo en polea.', 'muscle_group' => 'Tríceps', 'video_url' => 'https://youtube.com/watch?v=triceps-polea'],
            ['name' => 'Press Francés', 'description' => 'Extensión de tríceps acostado con barra.', 'muscle_group' => 'Tríceps', 'video_url' => 'https://youtube.com/watch?v=press-frances'],

            // CORE
            ['name' => 'Plancha', 'description' => 'Posición isométrica para core completo.', 'muscle_group' => 'Core', 'video_url' => 'https://youtube.com/watch?v=plancha'],
            ['name' => 'Crunches', 'description' => 'Flexión abdominal clásica.', 'muscle_group' => 'Core', 'video_url' => 'https://youtube.com/watch?v=crunches'],
            ['name' => 'Elevación de Piernas', 'description' => 'Elevación de piernas colgado o acostado.', 'muscle_group' => 'Core', 'video_url' => 'https://youtube.com/watch?v=elevacion-piernas'],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create($exercise);
        }
    }
}
