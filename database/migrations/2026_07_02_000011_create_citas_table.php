<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete()->comment('Paciente agendado');
            $table->foreignId('formula_medica_id')->nullable()->constrained('formulas_medicas')->nullOnDelete()->comment('Formula relacionada con la cita');
            $table->date('fecha_cita')->comment('Fecha de la cita');
            $table->time('hora_cita')->comment('Hora programada');
            $table->string('motivo', 80)->default('reclamacion')->comment('Motivo principal de la cita');
            $table->string('estado', 30)->default('programada')->comment('Estado de la cita');
            $table->text('observaciones')->nullable()->comment('Observaciones operativas de la cita');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};