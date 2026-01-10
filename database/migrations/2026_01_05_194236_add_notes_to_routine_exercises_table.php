<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('routine_exercises', function (Blueprint $col) {
        // Añadimos el campo de texto para las notas
        $col->text('notes')->nullable()->after('rest_time');
    });
}

public function down()
{
    Schema::table('routine_exercises', function (Blueprint $col) {
        $col->dropColumn('notes');
    });
}
};
