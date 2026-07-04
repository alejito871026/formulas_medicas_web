<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Usuario autenticable asociado al paciente');
            $table->string('tipo_documento', 20)->comment('Tipo de documento del paciente');
            $table->string('numero_documento', 20)->unique()->comment('Numero unico de identificacion');
            $table->string('nombres', 80)->comment('Nombres del paciente');
            $table->string('apellidos', 80)->comment('Apellidos del paciente');
            $table->date('fecha_nacimiento')->nullable()->comment('Fecha de nacimiento');
            $table->string('telefono', 20)->nullable()->comment('Telefono principal de contacto');
            $table->string('email', 120)->nullable()->comment('Correo electronico del paciente');
            $table->string('direccion', 150)->nullable()->comment('Direccion de residencia');
            $table->string('eps', 120)->nullable()->comment('Entidad promotora de salud');
            $table->string('municipio', 80)->nullable()->comment('Municipio de residencia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};