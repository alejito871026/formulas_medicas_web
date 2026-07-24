<?php

namespace App\Http\Controllers;

use App\Models\Eps;
use App\Models\Paciente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class PacienteController extends Controller
{
    private const TIPOS_DOCUMENTO = ['CC', 'TI', 'CE', 'PASAPORTE', 'PEP', 'PPT'];

    private const GEOGRAFIA = [
        'Antioquia' => ['Medellin', 'Envigado', 'Bello', 'Itagui', 'Rionegro'],
        'Cundinamarca' => ['Bogota', 'Soacha', 'Chia', 'Zipaquira', 'Facatativa'],
        'Valle del Cauca' => ['Cali', 'Palmira', 'Buenaventura', 'Tulua', 'Cartago'],
        'Atlantico' => ['Barranquilla', 'Soledad', 'Malambo', 'Puerto Colombia', 'Sabanalarga'],
        'Santander' => ['Bucaramanga', 'Floridablanca', 'Girón', 'Piedecuesta', 'Barrancabermeja'],
    ];

    private function datosCatalogoFormulario(): array
    {
        return [
            'tiposDocumento' => self::TIPOS_DOCUMENTO,
            'geografia' => self::GEOGRAFIA,
            'epsActivas' => Eps::query()->where('activo', true)->orderBy('nombre')->get(),
        ];
    }

    private function reglasMunicipioDepartamento(): array
    {
        $departamentos = array_keys(self::GEOGRAFIA);
        $municipios = collect(self::GEOGRAFIA)->flatten()->values()->all();

        return [
            'departamento' => ['required', Rule::in($departamentos)],
            'municipio' => ['required', Rule::in($municipios)],
        ];
    }

    private function municipioPerteneceDepartamento(string $departamento, string $municipio): bool
    {
        $municipios = self::GEOGRAFIA[$departamento] ?? [];

        return in_array($municipio, $municipios, true);
    }

    public function index(Request $request)
    {
        $estado = $request->query('estado', 'todos');
        $epsFiltro = $request->query('eps', 'todos');
        $busqueda = trim((string) $request->query('q', ''));
        $busquedaAplicada = mb_strlen($busqueda) > 5 ? $busqueda : '';

        $epsDisponibles = collect(
            Eps::query()->orderBy('nombre')->pluck('nombre')->all()
        )
            ->merge(
                Paciente::query()->whereNotNull('eps')->distinct()->pluck('eps')->all()
            )
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $pacientesQuery = Paciente::query()
            ->with('user')
            ->orderByDesc('id');

        if ($estado === 'activos') {
            $pacientesQuery->whereHas('user', fn ($query) => $query->where('activo', true));
        }

        if ($estado === 'inactivos') {
            $pacientesQuery->whereHas('user', fn ($query) => $query->where('activo', false));
        }

        if ($epsFiltro !== 'todos') {
            $pacientesQuery->where('eps', $epsFiltro);
        }

        if ($busquedaAplicada !== '') {
            $pacientesQuery->where(function ($query) use ($busquedaAplicada): void {
                $query->where('nombres', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('apellidos', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('numero_documento', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('email', 'like', "%{$busquedaAplicada}%")
                    ->orWhereHas('user', function ($userQuery) use ($busquedaAplicada): void {
                        $userQuery->where('name', 'like', "%{$busquedaAplicada}%")
                            ->orWhere('email', 'like', "%{$busquedaAplicada}%");
                    });
            });
        }

        $pacientes = $pacientesQuery
            ->paginate(12)
            ->withQueryString();

        return view('pacientes.index', [
            'pacientes' => $pacientes,
            'estado' => $estado,
            'epsFiltro' => $epsFiltro,
            'epsDisponibles' => $epsDisponibles,
            'epsActivas' => Eps::query()->where('activo', true)->orderBy('nombre')->pluck('nombre'),
            'busqueda' => $busqueda,
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'eps_destino' => ['required', Rule::exists('eps', 'nombre')->where(fn ($query) => $query->where('activo', true))],
            'archivo_pacientes' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'],
        ]);

        $rolePaciente = Role::query()->firstWhere('nombre', 'paciente');

        if (! $rolePaciente) {
            return redirect()->route('pacientes.index')->with('error', 'No existe el rol paciente. Ejecuta primero los seeders.');
        }

        try {
            $rows = $this->readImportRows($request->file('archivo_pacientes')->getRealPath());
        } catch (Throwable $exception) {
            return redirect()->route('pacientes.index')->with('error', 'No se pudo leer el archivo. Verifica que sea CSV o Excel valido.');
        }

        if (count($rows) === 0) {
            return redirect()->route('pacientes.index')->with('error', 'El archivo no contiene filas para importar.');
        }

        $creados = 0;
        $saltados = 0;
        $errores = [];

        foreach ($rows as $index => $row) {
            $fila = $index + 2;
            $payload = $this->normalizeImportRow($row);

            if ($this->rowIsEmpty($payload)) {
                continue;
            }

            $validator = Validator::make($payload, [
                'tipo_documento' => ['required', Rule::in(self::TIPOS_DOCUMENTO)],
                'numero_documento' => ['required', 'string', 'max:20', Rule::unique('pacientes', 'numero_documento')],
                'nombres' => ['required', 'string', 'max:80'],
                'apellidos' => ['required', 'string', 'max:80'],
                'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')],
                'telefono' => ['nullable', 'string', 'max:20'],
                'fecha_nacimiento' => ['nullable', 'date'],
                'direccion' => ['nullable', 'string', 'max:150'],
                ...$this->reglasMunicipioDepartamento(),
                'password' => ['nullable', 'string', 'min:8'],
            ]);

            if ($validator->fails()) {
                $saltados++;
                $errores[] = "Fila {$fila}: " . $validator->errors()->first();
                continue;
            }

            if (! $this->municipioPerteneceDepartamento($payload['departamento'], $payload['municipio'])) {
                $saltados++;
                $errores[] = "Fila {$fila}: el municipio no pertenece al departamento.";
                continue;
            }

            DB::transaction(function () use ($payload, $validated, $rolePaciente): void {
                $user = User::query()->create([
                    'name' => trim($payload['nombres'] . ' ' . $payload['apellidos']),
                    'email' => $payload['email'],
                    'password' => $payload['password'] ?: $payload['numero_documento'],
                    'role_id' => $rolePaciente->id,
                    'activo' => true,
                    'telefono' => $payload['telefono'] ?: null,
                    'direccion' => $payload['direccion'] ?: null,
                ]);

                Paciente::query()->create([
                    'user_id' => $user->id,
                    'tipo_documento' => $payload['tipo_documento'],
                    'numero_documento' => $payload['numero_documento'],
                    'nombres' => $payload['nombres'],
                    'apellidos' => $payload['apellidos'],
                    'fecha_nacimiento' => $payload['fecha_nacimiento'] ?: null,
                    'telefono' => $payload['telefono'] ?: null,
                    'email' => $payload['email'],
                    'direccion' => $payload['direccion'] ?: null,
                    'eps' => $validated['eps_destino'],
                    'departamento' => $payload['departamento'],
                    'municipio' => $payload['municipio'],
                ]);
            });

            $creados++;
        }

        if ($creados === 0) {
            $detalle = count($errores) > 0 ? ' ' . implode(' | ', array_slice($errores, 0, 3)) : '';
            return redirect()->route('pacientes.index')->with('error', 'No se importaron pacientes.' . $detalle);
        }

        $mensaje = "Importacion completada. Pacientes creados: {$creados}.";

        if ($saltados > 0) {
            $mensaje .= " Filas omitidas: {$saltados}.";
            if (count($errores) > 0) {
                $mensaje .= ' Ejemplos: ' . implode(' | ', array_slice($errores, 0, 3));
            }
        }

        return redirect()->route('pacientes.index')->with('success', $mensaje);
    }

    public function downloadTemplate()
    {
        $headers = [
            'tipo_documento',
            'numero_documento',
            'nombres',
            'apellidos',
            'email',
            'telefono',
            'fecha_nacimiento',
            'direccion',
            'departamento',
            'municipio',
            'password',
        ];

        $sample = [
            'CC',
            '1234567890',
            'Juan',
            'Perez',
            'juan.perez@example.com',
            '3001234567',
            '1990-05-14',
            'Calle 10 # 20-30',
            'Valle del Cauca',
            'Cartago',
            'Paciente123!',
        ];

        $filename = 'modelo_carga_pacientes.csv';

        return response()->streamDownload(function () use ($headers, $sample): void {
            $out = fopen('php://output', 'wb');
            if ($out === false) {
                return;
            }

            fputcsv($out, $headers);
            fputcsv($out, $sample);
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function create()
    {
        return view('pacientes.create', $this->datosCatalogoFormulario());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'tipo_documento' => ['required', Rule::in(self::TIPOS_DOCUMENTO)],
            'numero_documento' => ['required', 'string', 'max:20', 'unique:pacientes,numero_documento'],
            'nombres' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email_contacto' => ['nullable', 'email', 'max:120'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'eps' => ['required', Rule::exists('eps', 'nombre')->where(fn ($query) => $query->where('activo', true))],
            ...$this->reglasMunicipioDepartamento(),
        ]);

        if (! $this->municipioPerteneceDepartamento($validated['departamento'], $validated['municipio'])) {
            return back()->withErrors([
                'municipio' => 'El municipio seleccionado no pertenece al departamento.',
            ])->withInput();
        }

        $roleCliente = Role::query()->firstWhere('nombre', 'paciente');

        if (! $roleCliente) {
            return back()->withErrors([
                'role' => 'No existe el rol paciente. Ejecuta primero los seeders.',
            ])->withInput();
        }

        DB::transaction(function () use ($validated, $roleCliente): void {
            $user = User::query()->create([
                'name' => trim($validated['nombres'] . ' ' . $validated['apellidos']),
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role_id' => $roleCliente->id,
                'activo' => true,
            ]);

            Paciente::query()->create([
                'user_id' => $user->id,
                'tipo_documento' => $validated['tipo_documento'],
                'numero_documento' => $validated['numero_documento'],
                'nombres' => $validated['nombres'],
                'apellidos' => $validated['apellidos'],
                'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'email' => $validated['email_contacto'] ?? $validated['email'],
                'direccion' => $validated['direccion'] ?? null,
                'eps' => $validated['eps'],
                'departamento' => $validated['departamento'],
                'municipio' => $validated['municipio'],
            ]);
        });

        return redirect()->route('pacientes.index')->with('success', 'Paciente creado correctamente.');
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', [
            'paciente' => $paciente->load('user'),
            ...$this->datosCatalogoFormulario(),
        ]);
    }

    public function update(Request $request, Paciente $paciente): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($paciente->user_id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'tipo_documento' => ['required', Rule::in(self::TIPOS_DOCUMENTO)],
            'numero_documento' => ['required', 'string', 'max:20', Rule::unique('pacientes', 'numero_documento')->ignore($paciente->id)],
            'nombres' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email_contacto' => ['nullable', 'email', 'max:120'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'eps' => ['required', Rule::exists('eps', 'nombre')->where(fn ($query) => $query->where('activo', true))],
            ...$this->reglasMunicipioDepartamento(),
        ]);

        if (! $this->municipioPerteneceDepartamento($validated['departamento'], $validated['municipio'])) {
            return back()->withErrors([
                'municipio' => 'El municipio seleccionado no pertenece al departamento.',
            ])->withInput();
        }

        DB::transaction(function () use ($validated, $paciente): void {
            if ($paciente->user) {
                $paciente->user->name = trim($validated['nombres'] . ' ' . $validated['apellidos']);
                $paciente->user->email = $validated['email'];

                if (! empty($validated['password'])) {
                    $paciente->user->password = $validated['password'];
                }

                $paciente->user->save();
            }

            $paciente->update([
                'tipo_documento' => $validated['tipo_documento'],
                'numero_documento' => $validated['numero_documento'],
                'nombres' => $validated['nombres'],
                'apellidos' => $validated['apellidos'],
                'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'email' => $validated['email_contacto'] ?? $validated['email'],
                'direccion' => $validated['direccion'] ?? null,
                'eps' => $validated['eps'],
                'departamento' => $validated['departamento'],
                'municipio' => $validated['municipio'],
            ]);
        });

        return redirect()->route('pacientes.index')->with('success', 'Paciente actualizado correctamente.');
    }

    public function toggle(Paciente $paciente): RedirectResponse
    {
        if (! $paciente->user) {
            return redirect()->route('pacientes.index')->with('error', 'El paciente no tiene un usuario asociado.');
        }

        $paciente->user->activo = ! $paciente->user->activo;
        $paciente->user->save();

        $estado = $paciente->user->activo ? 'activado' : 'desactivado';

        return redirect()->route('pacientes.index')->with('success', "Paciente {$estado} correctamente.");
    }

    public function destroy(Paciente $paciente): RedirectResponse
    {
        DB::transaction(function () use ($paciente): void {
            $user = $paciente->user;
            $paciente->delete();

            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('pacientes.index')->with('success', 'Paciente eliminado correctamente.');
    }

    private function readImportRows(string $filePath): array
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);

        $sheet = $reader->load($filePath)->getActiveSheet();
        $rows = $sheet->toArray('', true, true, false);

        if (count($rows) < 2) {
            return [];
        }

        $header = array_shift($rows);
        $normalizedHeader = array_map(fn ($value) => $this->normalizeImportHeader((string) $value), $header);

        $result = [];

        foreach ($rows as $row) {
            $entry = [];
            foreach ($normalizedHeader as $index => $column) {
                if ($column === '') {
                    continue;
                }

                $entry[$column] = isset($row[$index]) ? trim((string) $row[$index]) : '';
            }
            $result[] = $entry;
        }

        return $result;
    }

    private function normalizeImportHeader(string $header): string
    {
        $normalized = Str::of(Str::ascii($header))
            ->lower()
            ->replace(['.', '-', ' '], '_')
            ->replace('__', '_')
            ->trim('_')
            ->toString();

        $aliases = [
            'tipo_doc' => 'tipo_documento',
            'tipodocumento' => 'tipo_documento',
            'documento' => 'numero_documento',
            'num_documento' => 'numero_documento',
            'correo' => 'email',
            'correo_electronico' => 'email',
            'fecha_nac' => 'fecha_nacimiento',
            'fecha_de_nacimiento' => 'fecha_nacimiento',
            'direccion_residencia' => 'direccion',
            'contrasena' => 'password',
            'clave' => 'password',
        ];

        return $aliases[$normalized] ?? $normalized;
    }

    private function normalizeImportRow(array $row): array
    {
        return [
            'tipo_documento' => trim((string) ($row['tipo_documento'] ?? '')),
            'numero_documento' => trim((string) ($row['numero_documento'] ?? '')),
            'nombres' => trim((string) ($row['nombres'] ?? '')),
            'apellidos' => trim((string) ($row['apellidos'] ?? '')),
            'email' => trim((string) ($row['email'] ?? '')),
            'telefono' => trim((string) ($row['telefono'] ?? '')),
            'fecha_nacimiento' => trim((string) ($row['fecha_nacimiento'] ?? '')),
            'direccion' => trim((string) ($row['direccion'] ?? '')),
            'departamento' => trim((string) ($row['departamento'] ?? '')),
            'municipio' => trim((string) ($row['municipio'] ?? '')),
            'password' => trim((string) ($row['password'] ?? '')),
        ];
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}