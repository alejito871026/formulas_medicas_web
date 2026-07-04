<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicamentos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique()->comment('Codigo interno o CUM del medicamento');
            $table->string('nombre', 120)->comment('Nombre comercial o generico');
            $table->string('principio_activo', 120)->nullable()->comment('Principio activo del medicamento');
            $table->string('presentacion', 80)->comment('Presentacion farmaceutica');
            $table->string('concentracion', 60)->nullable()->comment('Concentracion o dosis');
            $table->string('unidad_medida', 30)->nullable()->comment('Unidad de medida principal');
            $table->boolean('requiere_formula')->default(true)->comment('Indica si requiere formula medica');
            $table->text('observaciones')->nullable()->comment('Notas operativas sobre el medicamento');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicamentos');
    }
};