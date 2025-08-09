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
        // Normalização de datas (se o método estiver num controller, injete o request ou extraia helper)
        $data = $this->normalizeDate($filter['data'] ?? null);
        $dataInicio = $this->normalizeDate($filter['data_inicio'] ?? null);
        $dataFim = $this->normalizeDate($filter['data_fim'] ?? null);

        $query = Agendamento::query()
            ->when(!empty($filter['user_id']), fn($q) => $q->where('user_id', $filter['user_id']))
            ->when(!empty($filter['status']), fn($q) => $q->where('status', $filter['status']))
            ->when($data, fn($q) => $q->whereDate('data', $data))
            ->when($dataInicio && $dataFim, fn($q) => $q->whereBetween('data', [$dataInicio, $dataFim]))
            ->when($dataInicio && !$dataFim, fn($q) => $q->whereDate('data', '>=', $dataInicio))
            ->when($dataFim && !$dataInicio, fn($q) => $q->whereDate('data', '<=', $dataFim))
            ->orderByDesc('data')
            ->orderBy('horario');

        $perPage = isset($filter['per_page']) ? (int)$filter['per_page'] : 10;
        $page = isset($filter['page']) ? (int)$filter['page'] : 1;

        return $query
            ->paginate(perPage: $perPage, page: $page)
            ->appends($filter);
    }

    public function buscarPorId(string $id)
    {
        return Agendamento::where('uuid', $id)->first();
    }

    private function normalizeDate(?string $value): ?string
    {
        if (!$value) return null;

        $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y'];
        foreach ($formats as $f) {
            try {
                return Carbon::createFromFormat($f, $value)->format('Y-m-d');
            } catch (\Throwable) {
            }
        }
        return null;
    }
}
