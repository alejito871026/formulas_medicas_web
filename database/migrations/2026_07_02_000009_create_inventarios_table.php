<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicamento_id')->constrained('medicamentos')->restrictOnDelete()->comment('Medicamento controlado en inventario');
            $table->string('lote', 40)->comment('Lote del medicamento');
            $table->unsignedInteger('stock_actual')->default(0)->comment('Existencia disponible actual');
            $table->unsignedInteger('stock_minimo')->default(0)->comment('Nivel minimo para alerta');
            $table->date('fecha_vencimiento')->nullable()->comment('Fecha de vencimiento del lote');
            $table->string('ubicacion', 80)->nullable()->comment('Ubicacion fisica dentro del dispensario');
            $table->timestamps();

            $table->unique(['medicamento_id', 'lote']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};