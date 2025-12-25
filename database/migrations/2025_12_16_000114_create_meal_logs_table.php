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
        Schema::create('meal_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('consumed_date');
            $table->boolean('is_completed')->default(true);
            $table->string('photo_url')->nullable();
            $table->text('notes')->nullable();

            // Relación REAL con el plan asignado
            $table->foreignId('assigned_diet_id')
                ->constrained('assigned_diets')
                ->cascadeOnDelete();

            // Relación con la comida específica del plan
            $table->foreignId('diet_plan_meal_id')
                ->constrained('diet_plan_meal')
                ->cascadeOnDelete();
                
            $table->foreignId('user_id')
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
        Schema::dropIfExists('meal_logs');
    }
};
