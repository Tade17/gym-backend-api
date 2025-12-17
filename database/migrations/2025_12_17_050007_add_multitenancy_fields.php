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
        // Modificando planes : Ahora pertenecen a un entrenador
        Schema::table('plans', function (Blueprint $table) {
            $table->foreignId('trainer_id')
                ->nullable() // Nullable por si el Admin del sistema crea planes globales
                ->after('is_active')
                ->constrained('users'); // Se relaciona con el usuario (entrenador)
        });

        // Agregando la columna assigned_trainer_id, solo los clientes pertenecen a un entrenador
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('assigned_trainer_id')
                ->nullable() // Null para que el entrenador o admin no tenga entrenador asignado
                ->after('role')
                ->constrained('users') // Se relaciona con la misma tabla users (Recursiva)-> self-join
                ->nullOnDelete(); // Si el entrenador se va, el cliente queda "libre"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropForeign(['trainer_id']);
            $table->dropColumn('trainer_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['assigned_trainer_id']);
            $table->dropColumn('assigned_trainer_id');
        });
    }
};
