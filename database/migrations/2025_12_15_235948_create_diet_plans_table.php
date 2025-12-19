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
        Schema::create('diet_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->string('goal', 255);

            $table->foreignId('trainer_id')
                ->constrained('users')
                ->cascadeOnDelete();
                
            $table->foreignId('plan_id')
                ->constrained('plans')
                ->nullOnDelete(); //nullOnDelete para que no se borre la dieta si se borra el plan asociado

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diet_plans');
    }
};
