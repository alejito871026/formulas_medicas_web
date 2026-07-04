<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulas_medicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete()->comment('Paciente propietario de la formula');
            $table->string('numero_formula', 40)->unique()->comment('Consecutivo o identificador de la formula');
            $table->date('fecha_formula')->comment('Fecha de expedicion de la formula');
            $table->date('fecha_vencimiento')->nullable()->comment('Fecha de vencimiento o validez');
            $table->string('medico_tratante', 120)->nullable()->comment('Nombre del medico prescriptor');
            $table->string('estado', 30)->default('pendiente')->comment('Estado general de la formula');
            $table->text('observaciones')->nullable()->comment('Observaciones del proceso de validacion');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulas_medicas');
    }
};