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
        Schema::create('routines', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'elite']);

            $table->foreignId('trainer_id')
                ->constrained('users')
                ->cascadeOnDelete(); //cascadeOnDelete para eliminar rutinas si se elimina el entrenador

            $table->foreignId('plan_id')
                ->nullable()
                ->constrained('plans')
                ->nullOnDelete(); // Si se elimina el plan, establecer plan_id a null de tal manera que las rutinas no se eliminen

            $table->integer('estimated_duration')->unsigned(); // minutos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routines');
    }
};
