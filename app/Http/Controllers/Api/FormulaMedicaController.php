<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FormulaMedicaResource;
use App\Models\FormulaMedica;
use App\Models\Paciente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormulaMedicaController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', FormulaMedica::class);

        /** @var \App\Models\User $user */
        $user = auth('api')->user();
        $query = FormulaMedica::query()->with(['paciente.user.role']);

        if (! $user->hasRole('administrativo', 'despachador')) {
            $query->whereHas('paciente', function ($pacienteQuery) use ($user) {
                $pacienteQuery->where('user_id', $user->id);
            });
        }

        return FormulaMedicaResource::collection($query->orderByDesc('id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', FormulaMedica::class);

        $validated = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'numero_formula' => ['required', 'string', 'max:40', 'unique:formulas_medicas,numero_formula'],
            'fecha_formula' => ['required', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_formula'],
            'medico_tratante' => ['nullable', 'string', 'max:120'],
            'estado' => ['nullable', 'in:pendiente,en_validacion,parcial,entregada,vencida'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $paciente = Paciente::query()->findOrFail($validated['paciente_id']);
        $this->authorize('createForPaciente', [FormulaMedica::class, $paciente]);

        $formula = FormulaMedica::create([
            ...$validated,
            'estado' => $validated['estado'] ?? 'pendiente',
        ])->load(['paciente.user.role']);

        return (new FormulaMedicaResource($formula))
            ->response()
            ->setStatusCode(201);
    }

    public function show(FormulaMedica $formulas_medica)
    {
        $this->authorize('view', $formulas_medica);

        return new FormulaMedicaResource($formulas_medica->load(['paciente.user.role']));
    }

    public function update(Request $request, FormulaMedica $formulas_medica)
    {
        $this->authorize('update', $formulas_medica);

        $validated = $request->validate([
            'numero_formula' => ['sometimes', 'string', 'max:40', 'unique:formulas_medicas,numero_formula,' . $formulas_medica->id],
            'fecha_formula' => ['sometimes', 'date'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'medico_tratante' => ['nullable', 'string', 'max:120'],
            'estado' => ['sometimes', 'in:pendiente,en_validacion,parcial,entregada,vencida'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $formulas_medica->update($validated);

        return new FormulaMedicaResource($formulas_medica->fresh()->load(['paciente.user.role']));
    }

    public function destroy(FormulaMedica $formulas_medica): JsonResponse
    {
        $this->authorize('delete', $formulas_medica);

        $formulas_medica->delete();

        return response()->json(null, 204);
    }
}
