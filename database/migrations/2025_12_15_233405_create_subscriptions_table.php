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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('status')->default(1);//1 =activa , 0 =vencida
            
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete(); //cascadeOnDelete para que si se borra el usuario, se borre la suscripcion

            $table->foreignId('plan_id')
                ->constrained()
                ->restrictOnDelete(); //restrictOnDelete para que no se pueda borrar un plan si tiene suscripciones asociadas

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
