<?php

namespace App\Http\Controllers;

use App\Models\Eps;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EpsController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->query('estado', 'todos');
        $busqueda = trim((string) $request->query('q', ''));
        $busquedaAplicada = mb_strlen($busqueda) > 5 ? $busqueda : '';

        $epsQuery = Eps::query()->orderBy('nombre');

        if ($estado === 'activas') {
            $epsQuery->where('activo', true);
        }

        if ($estado === 'inactivas') {
            $epsQuery->where('activo', false);
        }

        if ($busquedaAplicada !== '') {
            $epsQuery->where(function ($query) use ($busquedaAplicada): void {
                $query->where('nombre', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('direccion', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('telefono', 'like', "%{$busquedaAplicada}%")
                    ->orWhere('nombre_contacto', 'like', "%{$busquedaAplicada}%");
            });
        }

        $epsList = $epsQuery
            ->paginate(15)
            ->withQueryString();

        return view('eps.index', [
            'epsList' => $epsList,
            'estado' => $estado,
            'busqueda' => $busqueda,
        ]);
    }

    public function edit(Eps $ep)
    {
        return view('eps.edit', [
            'ep' => $ep,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:120', 'unique:eps,nombre'],
            'direccion' => ['required', 'string', 'max:150'],
            'telefono' => ['required', 'string', 'max:30'],
            'nombre_contacto' => ['required', 'string', 'max:120'],
        ]);

        Eps::query()->create([
            'nombre' => $validated['nombre'],
            'direccion' => $validated['direccion'],
            'telefono' => $validated['telefono'],
            'nombre_contacto' => $validated['nombre_contacto'],
            'activo' => true,
        ]);

        return redirect()->route('eps.index')->with('success', 'EPS registrada correctamente.');
    }

    public function update(Request $request, Eps $ep): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:120', 'unique:eps,nombre,' . $ep->id],
            'direccion' => ['required', 'string', 'max:150'],
            'telefono' => ['required', 'string', 'max:30'],
            'nombre_contacto' => ['required', 'string', 'max:120'],
        ]);

        $ep->update([
            'nombre' => $validated['nombre'],
            'direccion' => $validated['direccion'],
            'telefono' => $validated['telefono'],
            'nombre_contacto' => $validated['nombre_contacto'],
        ]);

        return redirect()->route('eps.index')->with('success', 'EPS actualizada correctamente.');
    }

    public function toggle(Eps $ep): RedirectResponse
    {
        $ep->activo = ! $ep->activo;
        $ep->save();

        $estado = $ep->activo ? 'activada' : 'desactivada';

        return redirect()->route('eps.index')->with('success', "EPS {$estado} correctamente.");
    }

    public function destroy(Eps $ep): RedirectResponse
    {
        $enUso = \App\Models\Paciente::query()->where('eps', $ep->nombre)->exists();

        if ($enUso) {
            return redirect()->route('eps.index')->with('error', 'No puedes eliminar esta EPS porque esta asignada a clientes.');
        }

        $ep->delete();

        return redirect()->route('eps.index')->with('success', 'EPS eliminada correctamente.');
    }
}
