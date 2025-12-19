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
        Schema::create('assigned_routines', function (Blueprint $table) {
            $table->id();
            $table->date('assigned_date'); //fecha para el calendario (RF-16)
            $table->tinyInteger('status')->default(0); //0:pendiente , 1 :completada(RF-18)
            $table->tinyInteger('rating')->unsigned()->nullable();  // Calificación 1-5 estrellas (RF-23)

            $table->foreignId('routine_id')
                ->constrained('routines')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Opcional: El entrenador que asignó la rutina (Útil para RF-09)
            $table->foreignId('trainer_id')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigned_routines');
    }
};
