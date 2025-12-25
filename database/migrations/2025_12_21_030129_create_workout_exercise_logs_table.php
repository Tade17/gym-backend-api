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
        Schema::create('workout_exercise_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('actual_sets')->unsigned();
            $table->integer('actual_reps')->unsigned();
            $table->decimal('weight_used', 5, 2)->unsigned()->nullable();

            $table->foreignId('workout_log_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('exercise_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_exercise_logs');
    }
};
