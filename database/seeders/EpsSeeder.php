<?php

namespace Database\Seeders;

use App\Models\Eps;
use Illuminate\Database\Seeder;

class EpsSeeder extends Seeder
{
    public function run(): void
    {
        $eps = [
            ['nombre' => 'Nueva EPS', 'direccion' => 'Calle 26 # 69-76, Bogota', 'telefono' => '6013077022', 'nombre_contacto' => 'Mesa de Servicio Nueva EPS'],
            ['nombre' => 'Sura EPS', 'direccion' => 'Carrera 64B # 49A-30, Medellin', 'telefono' => '6044486115', 'nombre_contacto' => 'Soporte Empresarial Sura'],
            ['nombre' => 'Sanitas EPS', 'direccion' => 'Carrera 7 # 173-45, Bogota', 'telefono' => '6013759000', 'nombre_contacto' => 'Linea Integral Sanitas'],
            ['nombre' => 'Compensar EPS', 'direccion' => 'Avenida 68 # 49A-47, Bogota', 'telefono' => '6014441234', 'nombre_contacto' => 'Centro de Contacto Compensar'],
            ['nombre' => 'Coosalud EPS', 'direccion' => 'Calle 53 # 46-192, Barranquilla', 'telefono' => '6056510777', 'nombre_contacto' => 'Atencion Preferencial Coosalud'],
            ['nombre' => 'Salud Total EPS', 'direccion' => 'Carrera 45 # 94-67, Bogota', 'telefono' => '6014854555', 'nombre_contacto' => 'Atencion Nacional Salud Total'],
            ['nombre' => 'Famisanar EPS', 'direccion' => 'Calle 77A # 13-17, Bogota', 'telefono' => '6013078069', 'nombre_contacto' => 'Servicio al Usuario Famisanar'],
            ['nombre' => 'SOS EPS', 'direccion' => 'Avenida 3N # 23AN-20, Cali', 'telefono' => '6024857272', 'nombre_contacto' => 'Central de Citas SOS'],
            ['nombre' => 'Capital Salud EPS', 'direccion' => 'Carrera 13 # 32-76, Bogota', 'telefono' => '6013369650', 'nombre_contacto' => 'Contacto EPS Capital Salud'],
            ['nombre' => 'Mutual Ser EPS', 'direccion' => 'Calle 30 # 8B-25, Cartagena', 'telefono' => '6056500870', 'nombre_contacto' => 'Atencion Regional Mutual Ser'],
        ];

        foreach ($eps as $epsData) {
            Eps::query()->updateOrCreate(
                ['nombre' => $epsData['nombre']],
                [
                    'direccion' => $epsData['direccion'],
                    'telefono' => $epsData['telefono'],
                    'nombre_contacto' => $epsData['nombre_contacto'],
                    'activo' => true,
                ]
            );
        }
    }
}
