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
        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigned_routine_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('workout_date');

            $table->integer('duration')->unsigned()->nullable(); // minutos
            $table->decimal('weight_used', 5, 2)->unsigned()->nullable(); // kg
            $table->integer('reps')->unsigned()->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_logs');
    }
};
