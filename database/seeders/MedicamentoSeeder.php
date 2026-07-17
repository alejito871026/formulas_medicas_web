<?php

namespace Database\Seeders;

use App\Models\Medicamento;
use Illuminate\Database\Seeder;

class MedicamentoSeeder extends Seeder
{
    public function run(): void
    {
        $catalogoBase = [
            ['nombre' => 'Acetaminofen', 'principio_activo' => 'Paracetamol', 'presentacion' => 'Tableta', 'concentraciones' => ['500 mg', '650 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Ibuprofeno', 'principio_activo' => 'Ibuprofeno', 'presentacion' => 'Tableta', 'concentraciones' => ['200 mg', '400 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Naproxeno', 'principio_activo' => 'Naproxeno sodico', 'presentacion' => 'Tableta', 'concentraciones' => ['250 mg', '550 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Diclofenaco', 'principio_activo' => 'Diclofenaco sodico', 'presentacion' => 'Tableta', 'concentraciones' => ['50 mg', '75 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Omeprazol', 'principio_activo' => 'Omeprazol', 'presentacion' => 'Capsula', 'concentraciones' => ['20 mg', '40 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Esomeprazol', 'principio_activo' => 'Esomeprazol', 'presentacion' => 'Capsula', 'concentraciones' => ['20 mg', '40 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Lansoprazol', 'principio_activo' => 'Lansoprazol', 'presentacion' => 'Capsula', 'concentraciones' => ['15 mg', '30 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Losartan', 'principio_activo' => 'Losartan potasico', 'presentacion' => 'Tableta', 'concentraciones' => ['50 mg', '100 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Valsartan', 'principio_activo' => 'Valsartan', 'presentacion' => 'Tableta', 'concentraciones' => ['80 mg', '160 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Enalapril', 'principio_activo' => 'Enalapril maleato', 'presentacion' => 'Tableta', 'concentraciones' => ['10 mg', '20 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Amlodipino', 'principio_activo' => 'Amlodipino', 'presentacion' => 'Tableta', 'concentraciones' => ['5 mg', '10 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Metoprolol', 'principio_activo' => 'Metoprolol', 'presentacion' => 'Tableta', 'concentraciones' => ['50 mg', '100 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Carvedilol', 'principio_activo' => 'Carvedilol', 'presentacion' => 'Tableta', 'concentraciones' => ['12.5 mg', '25 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Hidroclorotiazida', 'principio_activo' => 'Hidroclorotiazida', 'presentacion' => 'Tableta', 'concentraciones' => ['25 mg', '50 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Furosemida', 'principio_activo' => 'Furosemida', 'presentacion' => 'Tableta', 'concentraciones' => ['20 mg', '40 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Atorvastatina', 'principio_activo' => 'Atorvastatina', 'presentacion' => 'Tableta', 'concentraciones' => ['20 mg', '40 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Rosuvastatina', 'principio_activo' => 'Rosuvastatina', 'presentacion' => 'Tableta', 'concentraciones' => ['10 mg', '20 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Simvastatina', 'principio_activo' => 'Simvastatina', 'presentacion' => 'Tableta', 'concentraciones' => ['20 mg', '40 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Metformina', 'principio_activo' => 'Metformina', 'presentacion' => 'Tableta', 'concentraciones' => ['500 mg', '850 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Glibenclamida', 'principio_activo' => 'Glibenclamida', 'presentacion' => 'Tableta', 'concentraciones' => ['5 mg', '10 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Dapagliflozina', 'principio_activo' => 'Dapagliflozina', 'presentacion' => 'Tableta', 'concentraciones' => ['5 mg', '10 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Empagliflozina', 'principio_activo' => 'Empagliflozina', 'presentacion' => 'Tableta', 'concentraciones' => ['10 mg', '25 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Insulina NPH', 'principio_activo' => 'Insulina humana NPH', 'presentacion' => 'Solucion inyectable', 'concentraciones' => ['100 UI/ml'], 'unidad' => 'UI', 'requiere_formula' => true],
            ['nombre' => 'Insulina regular', 'principio_activo' => 'Insulina humana regular', 'presentacion' => 'Solucion inyectable', 'concentraciones' => ['100 UI/ml'], 'unidad' => 'UI', 'requiere_formula' => true],
            ['nombre' => 'Levotiroxina', 'principio_activo' => 'Levotiroxina sodica', 'presentacion' => 'Tableta', 'concentraciones' => ['50 mcg', '100 mcg'], 'unidad' => 'mcg', 'requiere_formula' => true],
            ['nombre' => 'Prednisolona', 'principio_activo' => 'Prednisolona', 'presentacion' => 'Tableta', 'concentraciones' => ['5 mg', '20 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Dexametasona', 'principio_activo' => 'Dexametasona', 'presentacion' => 'Tableta', 'concentraciones' => ['4 mg', '8 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Loratadina', 'principio_activo' => 'Loratadina', 'presentacion' => 'Tableta', 'concentraciones' => ['10 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Cetirizina', 'principio_activo' => 'Cetirizina', 'presentacion' => 'Tableta', 'concentraciones' => ['10 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Desloratadina', 'principio_activo' => 'Desloratadina', 'presentacion' => 'Tableta', 'concentraciones' => ['5 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Salbutamol inhalador', 'principio_activo' => 'Salbutamol', 'presentacion' => 'Inhalador', 'concentraciones' => ['100 mcg/dosis'], 'unidad' => 'mcg', 'requiere_formula' => true],
            ['nombre' => 'Budesonida inhalador', 'principio_activo' => 'Budesonida', 'presentacion' => 'Inhalador', 'concentraciones' => ['200 mcg/dosis'], 'unidad' => 'mcg', 'requiere_formula' => true],
            ['nombre' => 'Beclometasona inhalador', 'principio_activo' => 'Beclometasona', 'presentacion' => 'Inhalador', 'concentraciones' => ['250 mcg/dosis'], 'unidad' => 'mcg', 'requiere_formula' => true],
            ['nombre' => 'Amoxicilina', 'principio_activo' => 'Amoxicilina', 'presentacion' => 'Capsula', 'concentraciones' => ['500 mg', '875 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Amoxicilina + acido clavulanico', 'principio_activo' => 'Amoxicilina/Clavulanato', 'presentacion' => 'Tableta', 'concentraciones' => ['500/125 mg', '875/125 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Azitromicina', 'principio_activo' => 'Azitromicina', 'presentacion' => 'Tableta', 'concentraciones' => ['500 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Claritromicina', 'principio_activo' => 'Claritromicina', 'presentacion' => 'Tableta', 'concentraciones' => ['250 mg', '500 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Ciprofloxacino', 'principio_activo' => 'Ciprofloxacino', 'presentacion' => 'Tableta', 'concentraciones' => ['500 mg', '750 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Levofloxacino', 'principio_activo' => 'Levofloxacino', 'presentacion' => 'Tableta', 'concentraciones' => ['500 mg', '750 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Trimetoprim sulfametoxazol', 'principio_activo' => 'Trimetoprim/Sulfametoxazol', 'presentacion' => 'Tableta', 'concentraciones' => ['160/800 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Metronidazol', 'principio_activo' => 'Metronidazol', 'presentacion' => 'Tableta', 'concentraciones' => ['250 mg', '500 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Nitrofurantoina', 'principio_activo' => 'Nitrofurantoina', 'presentacion' => 'Capsula', 'concentraciones' => ['100 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Fluconazol', 'principio_activo' => 'Fluconazol', 'presentacion' => 'Capsula', 'concentraciones' => ['150 mg', '200 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Ketoconazol crema', 'principio_activo' => 'Ketoconazol', 'presentacion' => 'Crema', 'concentraciones' => ['2 %'], 'unidad' => '%', 'requiere_formula' => false],
            ['nombre' => 'Clotrimazol crema', 'principio_activo' => 'Clotrimazol', 'presentacion' => 'Crema', 'concentraciones' => ['1 %'], 'unidad' => '%', 'requiere_formula' => false],
            ['nombre' => 'Nistatina suspension', 'principio_activo' => 'Nistatina', 'presentacion' => 'Suspension oral', 'concentraciones' => ['100000 UI/ml'], 'unidad' => 'UI', 'requiere_formula' => true],
            ['nombre' => 'Risperidona', 'principio_activo' => 'Risperidona', 'presentacion' => 'Tableta', 'concentraciones' => ['1 mg', '2 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Sertralina', 'principio_activo' => 'Sertralina', 'presentacion' => 'Tableta', 'concentraciones' => ['50 mg', '100 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Fluoxetina', 'principio_activo' => 'Fluoxetina', 'presentacion' => 'Capsula', 'concentraciones' => ['20 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Amitriptilina', 'principio_activo' => 'Amitriptilina', 'presentacion' => 'Tableta', 'concentraciones' => ['25 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Alprazolam', 'principio_activo' => 'Alprazolam', 'presentacion' => 'Tableta', 'concentraciones' => ['0.5 mg', '1 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Clonazepam', 'principio_activo' => 'Clonazepam', 'presentacion' => 'Tableta', 'concentraciones' => ['0.5 mg', '2 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Haloperidol', 'principio_activo' => 'Haloperidol', 'presentacion' => 'Tableta', 'concentraciones' => ['5 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Quetiapina', 'principio_activo' => 'Quetiapina', 'presentacion' => 'Tableta', 'concentraciones' => ['25 mg', '100 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Warfarina', 'principio_activo' => 'Warfarina sodica', 'presentacion' => 'Tableta', 'concentraciones' => ['5 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Rivaroxaban', 'principio_activo' => 'Rivaroxaban', 'presentacion' => 'Tableta', 'concentraciones' => ['10 mg', '20 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Apixaban', 'principio_activo' => 'Apixaban', 'presentacion' => 'Tableta', 'concentraciones' => ['2.5 mg', '5 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Acido acetilsalicilico', 'principio_activo' => 'Acido acetilsalicilico', 'presentacion' => 'Tableta', 'concentraciones' => ['100 mg', '500 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Clopidogrel', 'principio_activo' => 'Clopidogrel', 'presentacion' => 'Tableta', 'concentraciones' => ['75 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Heparina sodica', 'principio_activo' => 'Heparina', 'presentacion' => 'Solucion inyectable', 'concentraciones' => ['5000 UI/ml'], 'unidad' => 'UI', 'requiere_formula' => true],
            ['nombre' => 'Sulfato ferroso', 'principio_activo' => 'Sulfato ferroso', 'presentacion' => 'Tableta', 'concentraciones' => ['325 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Acido folico', 'principio_activo' => 'Acido folico', 'presentacion' => 'Tableta', 'concentraciones' => ['1 mg', '5 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Calcio + vitamina D', 'principio_activo' => 'Carbonato de calcio / Vitamina D3', 'presentacion' => 'Tableta masticable', 'concentraciones' => ['600 mg + 400 UI'], 'unidad' => 'mg/UI', 'requiere_formula' => false],
            ['nombre' => 'Vitamina C', 'principio_activo' => 'Acido ascorbico', 'presentacion' => 'Tableta', 'concentraciones' => ['500 mg', '1000 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Zinc', 'principio_activo' => 'Sulfato de zinc', 'presentacion' => 'Tableta', 'concentraciones' => ['20 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Albendazol', 'principio_activo' => 'Albendazol', 'presentacion' => 'Tableta', 'concentraciones' => ['400 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Ivermectina', 'principio_activo' => 'Ivermectina', 'presentacion' => 'Tableta', 'concentraciones' => ['6 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Mebendazol', 'principio_activo' => 'Mebendazol', 'presentacion' => 'Tableta', 'concentraciones' => ['100 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Loperamida', 'principio_activo' => 'Loperamida', 'presentacion' => 'Capsula', 'concentraciones' => ['2 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Ondansetron', 'principio_activo' => 'Ondansetron', 'presentacion' => 'Tableta', 'concentraciones' => ['4 mg', '8 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Dimenhidrinato', 'principio_activo' => 'Dimenhidrinato', 'presentacion' => 'Tableta', 'concentraciones' => ['50 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Lactulosa', 'principio_activo' => 'Lactulosa', 'presentacion' => 'Jarabe', 'concentraciones' => ['667 mg/ml'], 'unidad' => 'mg/ml', 'requiere_formula' => false],
            ['nombre' => 'Psyllium', 'principio_activo' => 'Plantago ovata', 'presentacion' => 'Polvo', 'concentraciones' => ['3.5 g/sobre'], 'unidad' => 'g', 'requiere_formula' => false],
            ['nombre' => 'Clorfeniramina', 'principio_activo' => 'Clorfeniramina', 'presentacion' => 'Tableta', 'concentraciones' => ['4 mg'], 'unidad' => 'mg', 'requiere_formula' => false],
            ['nombre' => 'Mometasona nasal', 'principio_activo' => 'Mometasona', 'presentacion' => 'Spray nasal', 'concentraciones' => ['50 mcg/dosis'], 'unidad' => 'mcg', 'requiere_formula' => true],
            ['nombre' => 'Fluticasona nasal', 'principio_activo' => 'Fluticasona', 'presentacion' => 'Spray nasal', 'concentraciones' => ['50 mcg/dosis'], 'unidad' => 'mcg', 'requiere_formula' => true],
            ['nombre' => 'Tobramicina oftalmica', 'principio_activo' => 'Tobramicina', 'presentacion' => 'Gotas oftalmicas', 'concentraciones' => ['0.3 %'], 'unidad' => '%', 'requiere_formula' => true],
            ['nombre' => 'Ciprofloxacino oftalmico', 'principio_activo' => 'Ciprofloxacino', 'presentacion' => 'Gotas oftalmicas', 'concentraciones' => ['0.3 %'], 'unidad' => '%', 'requiere_formula' => true],
            ['nombre' => 'Lagrimas artificiales', 'principio_activo' => 'Hipromelosa', 'presentacion' => 'Gotas oftalmicas', 'concentraciones' => ['0.3 %'], 'unidad' => '%', 'requiere_formula' => false],
            ['nombre' => 'Mupirocina', 'principio_activo' => 'Mupirocina', 'presentacion' => 'Unguento', 'concentraciones' => ['2 %'], 'unidad' => '%', 'requiere_formula' => true],
            ['nombre' => 'Fusidato de sodio', 'principio_activo' => 'Acido fusidico', 'presentacion' => 'Crema', 'concentraciones' => ['2 %'], 'unidad' => '%', 'requiere_formula' => true],
            ['nombre' => 'Hidrocortisona crema', 'principio_activo' => 'Hidrocortisona', 'presentacion' => 'Crema', 'concentraciones' => ['1 %'], 'unidad' => '%', 'requiere_formula' => false],
            ['nombre' => 'Betametasona crema', 'principio_activo' => 'Betametasona', 'presentacion' => 'Crema', 'concentraciones' => ['0.05 %'], 'unidad' => '%', 'requiere_formula' => true],
            ['nombre' => 'Lidocaina', 'principio_activo' => 'Lidocaina', 'presentacion' => 'Solucion inyectable', 'concentraciones' => ['2 %'], 'unidad' => '%', 'requiere_formula' => true],
            ['nombre' => 'Tramadol', 'principio_activo' => 'Tramadol', 'presentacion' => 'Capsula', 'concentraciones' => ['50 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Codeina + acetaminofen', 'principio_activo' => 'Codeina/Paracetamol', 'presentacion' => 'Tableta', 'concentraciones' => ['30/500 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Morfina', 'principio_activo' => 'Sulfato de morfina', 'presentacion' => 'Tableta', 'concentraciones' => ['10 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Gabapentina', 'principio_activo' => 'Gabapentina', 'presentacion' => 'Capsula', 'concentraciones' => ['300 mg', '600 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
            ['nombre' => 'Pregabalina', 'principio_activo' => 'Pregabalina', 'presentacion' => 'Capsula', 'concentraciones' => ['75 mg', '150 mg'], 'unidad' => 'mg', 'requiere_formula' => true],
        ];

        $consecutivo = 1;

        foreach ($catalogoBase as $item) {
            foreach ($item['concentraciones'] as $concentracion) {
                $codigo = 'MED-' . str_pad((string) $consecutivo, 5, '0', STR_PAD_LEFT);

                Medicamento::query()->updateOrCreate(
                    ['codigo' => $codigo],
                    [
                        'nombre' => $item['nombre'],
                        'principio_activo' => $item['principio_activo'],
                        'presentacion' => $item['presentacion'],
                        'concentracion' => $concentracion,
                        'unidad_medida' => $item['unidad'],
                        'requiere_formula' => $item['requiere_formula'],
                        'observaciones' => $item['requiere_formula']
                            ? 'Dispensacion sujeta a validacion medica vigente.'
                            : 'Producto de venta libre con control basico de inventario.',
                    ]
                );

                $consecutivo++;
            }
        }
    }
}
