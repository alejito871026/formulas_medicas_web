<?php

namespace App\Http\Controllers;

use App\Models\Eps;
use App\Models\Paciente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            'busqueda' => $busqueda,
        ]);
    }

    public function create()
    {
        return view('pacientes.create', $this->datosCatalogoFormulario());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
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

        $roleCliente = Role::query()->firstWhere('nombre', 'cliente');

        if (! $roleCliente) {
            return back()->withErrors([
                'role' => 'No existe el rol cliente. Ejecuta primero los seeders.',
            ])->withInput();
        }

        DB::transaction(function () use ($validated, $roleCliente): void {
            $user = User::query()->create([
                'name' => $validated['name'],
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

        return redirect()->route('pacientes.index')->with('success', 'Cliente creado correctamente.');
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
            'name' => ['required', 'string', 'max:120'],
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
                $paciente->user->name = $validated['name'];
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

        return redirect()->route('pacientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function toggle(Paciente $paciente): RedirectResponse
    {
        if (! $paciente->user) {
            return redirect()->route('pacientes.index')->with('error', 'El paciente no tiene un usuario asociado.');
        }

        $paciente->user->activo = ! $paciente->user->activo;
        $paciente->user->save();

        $estado = $paciente->user->activo ? 'activado' : 'desactivado';

        return redirect()->route('pacientes.index')->with('success', "Cliente {$estado} correctamente.");
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

        return redirect()->route('pacientes.index')->with('success', 'Cliente eliminado correctamente.');
    }
}