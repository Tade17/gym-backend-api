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
        Schema::create('diet_plan_meal', function (Blueprint $table) {
            $table->id();
            $table->time('suggested_time');
            $table->enum('meal_type', ['breakfast', 'lunch', 'snack', 'dinner']); 
            $table->date('day_of_week'); 

            $table->foreignId('diet_plan_id')->constrained('diet_plans')->cascadeOnDelete();
            $table->foreignId('meal_id')->constrained('meals')->cascadeOnDelete();
            $table->timestamps();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diet_plan_meal');
    }
};
