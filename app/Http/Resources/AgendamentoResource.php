<?php

namespace App\Http\Resources;

use App\Http\Resources\LocalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgendamentoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'titulo'      => $this->titulo,
            'data'        => $this->data?->format('d/m/Y'),
            'hora_inicio' => $this->hora_inicio,
            'hora_final'  => $this->hora_final,
            'observacoes' => $this->observacoes,
            'status'      => $this->status,
            'user'        => new UserResource($this->whenLoaded('user')),
            'local'       => new LocalResource($this->whenLoaded('local')),
            'created_at'  => $this->created_at?->format('d/m/Y H:i'),
            'updated_at'  => $this->updated_at?->format('d/m/Y H:i'),
        ];
    }
}