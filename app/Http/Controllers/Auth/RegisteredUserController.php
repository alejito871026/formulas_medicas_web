<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Eps;
use App\Models\Paciente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    private const TIPOS_DOCUMENTO = ['CC', 'TI', 'CE', 'PASAPORTE', 'PEP', 'PPT'];

    private const GEOGRAFIA = [
        'Antioquia' => ['Medellin', 'Envigado', 'Bello', 'Itagui', 'Rionegro'],
        'Cundinamarca' => ['Bogota', 'Soacha', 'Chia', 'Zipaquira', 'Facatativa'],
        'Valle del Cauca' => ['Cali', 'Palmira', 'Buenaventura', 'Tulua', 'Cartago'],
        'Atlantico' => ['Barranquilla', 'Soledad', 'Malambo', 'Puerto Colombia', 'Sabanalarga'],
        'Santander' => ['Bucaramanga', 'Floridablanca', 'Girón', 'Piedecuesta', 'Barrancabermeja'],
    ];

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

    public function create(): Response
    {
        return response()
            ->view('auth.register', [
                'tiposDocumento' => self::TIPOS_DOCUMENTO,
                'geografia' => self::GEOGRAFIA,
                'epsActivas' => Eps::query()->where('activo', true)->orderBy('nombre')->get(),
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'tipo_documento' => ['required', Rule::in(self::TIPOS_DOCUMENTO)],
            'numero_documento' => ['required', 'string', 'max:20', 'unique:pacientes,numero_documento'],
            'nombres' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'eps' => ['required', Rule::exists('eps', 'nombre')->where(fn ($query) => $query->where('activo', true))],
            ...$this->reglasMunicipioDepartamento(),
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        if (! $this->municipioPerteneceDepartamento($validated['departamento'], $validated['municipio'])) {
            return back()->withErrors([
                'municipio' => 'El municipio seleccionado no pertenece al departamento.',
            ])->withInput();
        }

        $rolePaciente = Role::query()->firstWhere('nombre', 'cliente');

        if (! $rolePaciente) {
            return back()->withErrors([
                'registro' => 'No existe el rol paciente configurado en el sistema.',
            ])->withInput();
        }

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = DB::transaction(function () use ($validated, $rolePaciente): User {
            $user = User::query()->create([
                'name' => trim($validated['nombres'] . ' ' . $validated['apellidos']),
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role_id' => $rolePaciente->id,
                'activo' => true,
                'telefono' => $validated['telefono'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'avatar' => $validated['avatar'] ?? null,
            ]);

            Paciente::query()->create([
                'user_id' => $user->id,
                'tipo_documento' => $validated['tipo_documento'],
                'numero_documento' => $validated['numero_documento'],
                'nombres' => $validated['nombres'],
                'apellidos' => $validated['apellidos'],
                'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'email' => $validated['email'],
                'direccion' => $validated['direccion'] ?? null,
                'eps' => $validated['eps'],
                'departamento' => $validated['departamento'],
                'municipio' => $validated['municipio'],
            ]);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
