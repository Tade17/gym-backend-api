<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Usamos VARCHAR(1) -> Es un String que solo permite 1 caracter.
        // Es perfecto para guardar 'M', 'F'.
        DB::statement("ALTER TABLE users MODIFY COLUMN gender VARCHAR(1) NULL");
    }

    public function down(): void
    {
        // Si revertimos, lo dejamos como texto normal un poco más largo por seguridad
        DB::statement("ALTER TABLE users MODIFY COLUMN gender VARCHAR(10) NULL");
    }
};