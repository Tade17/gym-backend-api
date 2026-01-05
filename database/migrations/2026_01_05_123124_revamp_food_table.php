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
        Schema::table('food', function (Blueprint $table) {
            
            // 1. SOLUCIÓN: Verificamos si las columnas existen antes de intentar borrarlas.
            // Si no existen, Laravel ignorará estas líneas y no dará error.
            if (Schema::hasColumn('food', 'protein')) {
                $table->dropColumn(['protein', 'carbohydrates', 'fats', 'serving_size']);
            }

            // 2. Agregamos las nuevas columnas (si no existen ya)
            if (!Schema::hasColumn('food', 'category')) {
                $table->string('category', 50)->after('name'); 
            }
            
            if (!Schema::hasColumn('food', 'image_url')) {
                $table->string('image_url')->nullable()->after('calories_per_100g');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios si fuera necesario
        Schema::table('food', function (Blueprint $table) {
            $table->decimal('protein', 8, 2)->default(0);
            $table->decimal('carbohydrates', 8, 2)->default(0);
            $table->decimal('fats', 8, 2)->default(0);
            $table->string('serving_size')->nullable();
            $table->dropColumn(['category', 'image_url']);
        });
    }
};
