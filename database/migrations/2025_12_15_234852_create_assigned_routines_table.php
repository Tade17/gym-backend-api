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

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('routine_id')
                ->constrained()
                ->restrictOnDelete();

            $table->date('assigned_date');

            $table->tinyInteger('status')
                ->default(0)
                ->comment('0=pending, 1=completed, 2=skipped');
                
            $table->tinyInteger('rating')
                ->unsigned()
                ->nullable()
                ->comment('1 to 5 stars');

            $table->timestamps();

            $table->unique(['user_id', 'routine_id', 'assigned_date']);
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
