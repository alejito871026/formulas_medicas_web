<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_medicamento_id')->constrained('formula_medicamento')->cascadeOnDelete()->comment('Item formulado sujeto a entrega');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Funcionario que registra la entrega');
            $table->date('fecha_entrega')->comment('Fecha de entrega o compromiso');
            $table->unsignedInteger('cantidad_entregada')->comment('Cantidad efectivamente entregada');
            $table->string('estado_entrega', 30)->default('pendiente')->comment('Estado de la entrega');
            $table->date('fecha_estimada')->nullable()->comment('Fecha estimada si queda pendiente');
            $table->text('observaciones')->nullable()->comment('Notas del proceso de entrega');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};