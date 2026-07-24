<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $clienteRole = DB::table('roles')->where('nombre', 'cliente')->first();
        $pacienteRole = DB::table('roles')->where('nombre', 'paciente')->first();

        if ($clienteRole && $pacienteRole) {
            DB::table('users')
                ->where('role_id', $clienteRole->id)
                ->update(['role_id' => $pacienteRole->id]);

            DB::table('roles')->where('id', $clienteRole->id)->delete();

            return;
        }

        if ($clienteRole) {
            DB::table('roles')
                ->where('id', $clienteRole->id)
                ->update([
                    'nombre' => 'paciente',
                    'descripcion' => 'Paciente o usuario final del sistema',
                ]);
        }
    }

    public function down(): void
    {
        $clienteRole = DB::table('roles')->where('nombre', 'cliente')->first();
        $pacienteRole = DB::table('roles')->where('nombre', 'paciente')->first();

        if ($clienteRole || ! $pacienteRole) {
            return;
        }

        DB::table('roles')
            ->where('id', $pacienteRole->id)
            ->update([
                'nombre' => 'cliente',
                'descripcion' => 'Paciente o usuario final del sistema',
            ]);
    }
};