<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AgendamentoResource;
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'tipo'       => $this->tipo,
            'roles'      => $this->getRoleNames(),
            'permissions'=> $this->whenLoaded('permissions', fn () => $this->getAllPermissions()->pluck('name')),
            'agendamentos_count' => $this->whenCounted('agendamentos'),
            'agendamentos'       => AgendamentoResource::collection($this->whenLoaded('agendamentos')),
            'created_at' => $this->created_at?->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i'),

        ];
    }
}