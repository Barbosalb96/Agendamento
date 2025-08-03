<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domains\Agendamento\Entities\Agendamento;
use App\Domains\Agendamento\Repositories\ContratoAgendamentoRepositorio;
use Carbon\Carbon;

class EloquentAgendamentoRepositorio implements ContratoAgendamentoRepositorio
{
    public function salvar(array $agendamento)
    {
        return Agendamento::create($agendamento);
    }

    public function existePorData(int $prestadorId, Carbon $dataHora): bool
    {
        return Agendamento::where('prestador_id', $prestadorId)
            ->where('data_hora', $dataHora)
            ->exists();
    }

    public function cancelar(string $id): void
    {
        Agendamento::where('uuid', $id)->delete();
    }

    public function buscar()
    {
        return Agendamento::all();
    }

    public function buscarPorId(string $id)
    {
        return Agendamento::where('uuid', $id)->first();
    }
}
