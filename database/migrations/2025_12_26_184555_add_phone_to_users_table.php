<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Agregamos el teléfono después del email, nullable por si ya hay usuarios creados
            $table->string('phone_number', 20)->nullable()->after('email');
        });

        Schema::table('routines', function (Blueprint $table) {
            $table->integer('estimated_calories')->unsigned()->nullable()->after('estimated_duration');
        });

        Schema::table('workout_logs', function (Blueprint $table) {
            $table->decimal('calories_burned', 8, 2)->default(0)->after('duration');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number');
        });
    }
};
