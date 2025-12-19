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
        Schema::create('assigned_diets', function (Blueprint $table) {
            $table->id();

            $table->dateTime('start_date');
            $table->date('end_date')->nullable();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('diet_plan_id')
                ->constrained('diet_plans')
                ->cascadeOnDelete();

            //El entrenador que asignó la dieta (Útil para RF-09)
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
        Schema::dropIfExists('assigned_diets');
    }
};
