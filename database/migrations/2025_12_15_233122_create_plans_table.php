<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes; //para manejo de eliminaciones logicas, es decir se 
//deja de vender un plan pero se mantiene el historial
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['basic', 'Pro', 'Personalized']);
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2)->unsigned();
            $table->integer('duration_days')->unsigned(); // 30, 90, 365
            $table->boolean('is_active')->default(true);
            //para ver que entrenador ofrece este plan y a que precio
            $table->foreignId('trainer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
