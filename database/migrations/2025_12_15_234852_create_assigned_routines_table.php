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
            $table->date('assigned_date');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('rating')->unsigned()->nullable();

            $table->foreignId('routine_id')
                ->constrained('routines')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
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
        Schema::dropIfExists('assigned_routines');
    }
};
