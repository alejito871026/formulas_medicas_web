<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DespachadorController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->query('estado', 'todos');
        $busqueda = trim((string) $request->query('q', ''));
        $busquedaAplicada = mb_strlen($busqueda) > 5 ? $busqueda : '';

        $query = User::query()
            ->with('role')
            ->whereHas('role', fn ($roleQuery) => $roleQuery->where('nombre', 'despachador'))
            ->orderBy('name');

        if ($estado === 'activos') {
            $query->where('activo', true);
        }

        if ($estado === 'inactivos') {
            $query->where('activo', false);
        }

        if ($busquedaAplicada !== '') {
            $query->where(function ($subQuery) use ($busquedaAplicada): void {
                $subQuery->where('name', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('email', 'like', "%{$busquedaAplicada}%");
            });
        }

        $despachadores = $query
            ->paginate(12)
            ->withQueryString();

        return view('despachadores.index', [
            'despachadores' => $despachadores,
            'estado' => $estado,
            'busqueda' => $busqueda,
        ]);
    }

    public function create()
    {
        return view('despachadores.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $rolDespachador = Role::query()->firstWhere('nombre', 'despachador');

        if (! $rolDespachador) {
            return back()->withErrors([
                'role' => 'No existe el rol despachador. Ejecuta seeders de roles.',
            ])->withInput();
        }

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role_id' => $rolDespachador->id,
            'activo' => true,
        ]);

        return redirect()->route('despachadores.index')->with('success', 'Despachador creado correctamente.');
    }

    public function edit(User $despachador)
    {
        abort_unless($despachador->role?->nombre === 'despachador', 404);

        return view('despachadores.edit', [
            'despachador' => $despachador,
        ]);
    }

    public function update(Request $request, User $despachador): RedirectResponse
    {
        abort_unless($despachador->role?->nombre === 'despachador', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($despachador->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $despachador->name = $validated['name'];
        $despachador->email = $validated['email'];

        if (! empty($validated['password'])) {
            $despachador->password = $validated['password'];
        }

        $despachador->save();

        return redirect()->route('despachadores.index')->with('success', 'Despachador actualizado correctamente.');
    }

    public function toggle(User $despachador): RedirectResponse
    {
        abort_unless($despachador->role?->nombre === 'despachador', 404);

        $despachador->activo = ! $despachador->activo;
        $despachador->save();

        $estado = $despachador->activo ? 'activado' : 'desactivado';

        return redirect()->route('despachadores.index')->with('success', "Despachador {$estado} correctamente.");
    }

    public function destroy(User $despachador): RedirectResponse
    {
        abort_unless($despachador->role?->nombre === 'despachador', 404);

        if ((int) Auth::id() === (int) $despachador->id) {
            return redirect()->route('despachadores.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $despachador->delete();

        return redirect()->route('despachadores.index')->with('success', 'Despachador eliminado correctamente.');
    }
}
