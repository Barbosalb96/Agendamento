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

    public function cancelar(string $id, array $data): Agendamento
    {
        $agendamento = Agendamento::with('user')
            ->where('uuid', $id)
            ->firstOrFail();
        $agendamento->observacao = $data['observacao'] ?? null;
        $agendamento->save();
        $agendamento->delete();

        return $agendamento;
    }

    public function buscar(array $filter)
    {
        return Agendamento::query()
            ->when(! empty($filter['data']), function ($query) use ($filter) {
                $query->whereDate('data', $filter['data']);
            })
            ->paginate(perPage: $filter['per_page'] ?? 10, page: $filter['page'] ?? 1);
    }

    public function buscarPorId(string $id)
    {
        return Agendamento::where('uuid', $id)->first();
    }
}
