<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgendamentosResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'data' => Carbon::parse($this->data)->format('d/m/Y'),
            'horario' => Carbon::parse($this->horario)->format('H:i'),
            'grupo' => $this->grupo,
            'quantidade' => $this->quantidade,
            'observacao' => $this->observacao,
        ];
    }
}
