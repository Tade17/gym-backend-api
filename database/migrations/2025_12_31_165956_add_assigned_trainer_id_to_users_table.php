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
        Schema::table('users', function (Blueprint $table) {
            // Creamos la columna que guardará el ID del entrenador
            // 'nullable' porque los entrenadores o admins no tienen entrenador asignado
            $table->unsignedBigInteger('assigned_trainer_id')->nullable()->after('profile_photo');

            // Hacemos la relación: Este ID debe existir en la misma tabla 'users'
            $table->foreign('assigned_trainer_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null'); // Si borran al entrenador, el alumno queda sin asignar (no se borra)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['assigned_trainer_id']);
            $table->dropColumn('assigned_trainer_id');
        });
    }
};