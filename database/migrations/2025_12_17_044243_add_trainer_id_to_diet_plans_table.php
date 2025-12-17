<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('diet_plans', function (Blueprint $table) {
            // Agregamos la columna, puede ser nula por si la crea el "Sistema" (Admin)
            $table->foreignId('trainer_id')
                ->nullable() // Opcional, o quítalo si siempre debe tener creador
                ->after('description') // Para que se vea ordenado
                ->constrained('users'); // Se conecta con la tabla users
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diet_plans', function (Blueprint $table) {
            $table->dropForeign(['trainer_id']);
            $table->dropColumn('trainer_id');
        });
    }
};
