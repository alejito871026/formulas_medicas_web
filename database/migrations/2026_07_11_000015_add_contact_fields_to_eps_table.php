<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eps', function (Blueprint $table) {
            $table->string('direccion', 150)->nullable()->after('nombre');
            $table->string('telefono', 30)->nullable()->after('direccion');
            $table->string('nombre_contacto', 120)->nullable()->after('telefono');
        });
    }

    public function down(): void
    {
        Schema::table('eps', function (Blueprint $table) {
            $table->dropColumn(['direccion', 'telefono', 'nombre_contacto']);
        });
    }
};
