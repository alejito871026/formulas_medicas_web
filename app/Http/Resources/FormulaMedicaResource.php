<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormulaMedicaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero_formula' => $this->numero_formula,
            'fecha_formula' => $this->fecha_formula?->toDateString(),
            'fecha_vencimiento' => $this->fecha_vencimiento?->toDateString(),
            'medico_tratante' => $this->medico_tratante,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
            'paciente' => [
                'id' => $this->paciente?->id,
                'nombre_completo' => trim(($this->paciente?->nombres ?? '') . ' ' . ($this->paciente?->apellidos ?? '')),
                'documento' => $this->paciente?->numero_documento,
                'email' => $this->paciente?->email,
            ],
            'usuario_responsable' => [
                'id' => $this->paciente?->user?->id,
                'name' => $this->paciente?->user?->name,
                'email' => $this->paciente?->user?->email,
                'rol' => $this->paciente?->user?->role?->nombre,
            ],
            'creado_en' => $this->created_at?->toDateTimeString(),
            'actualizado_en' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
