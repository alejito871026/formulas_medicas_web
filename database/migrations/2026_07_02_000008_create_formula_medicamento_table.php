<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formula_medicamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_medica_id')->constrained('formulas_medicas')->cascadeOnDelete()->comment('Formula medica asociada');
            $table->foreignId('medicamento_id')->constrained('medicamentos')->restrictOnDelete()->comment('Medicamento prescrito');
            $table->unsignedInteger('cantidad_formulada')->comment('Cantidad prescrita en la formula');
            $table->unsignedInteger('cantidad_entregada')->default(0)->comment('Cantidad ya entregada');
            $table->string('dosis', 80)->nullable()->comment('Dosis prescrita');
            $table->string('frecuencia', 80)->nullable()->comment('Frecuencia de uso');
            $table->string('estado_item', 30)->default('pendiente')->comment('Estado del item formulado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formula_medicamento');
    }
};