<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domains\GestaoDias\Repositories\GestaoDiasRepositorio;
use App\Models\DiasFechados;

class EloquentGestaoDias implements GestaoDiasRepositorio
{
    public function __construct(
        private DiasFechados $diasFechados,
    ) {}

    public function listar(array $filtro)
    {
        return $this->diasFechados->query()
            ->when(! empty($filtro['data']), function ($query) use ($filtro) {
                $query->where('data', '=', $filtro['data']);
            })
            ->when(! empty($filtro['tipo']), function ($query) use ($filtro) {
                $query->where('tipo', '=', $filtro['tipo']);
            })
            ->when(! empty($filtro['data_inicio']) && ! empty($filtro['data_fim']), function ($query) use ($filtro) {
                $query->whereBetween('data', [$filtro['data_inicio'], $filtro['data_fim']]);
            })
            ->orderBy('data', 'desc')
            ->paginate(perPage: $filtro['per_page'] ?? 15, page: $filtro['page'] ?? 1);
    }

    public function buscar(int $id)
    {
        return $this->diasFechados->find($id);
    }

    public function create(array $data)
    {
        $diaFechado = $this->diasFechados->create($data);

        return $diaFechado;
    }

    public function update(array $data, int $id): bool
    {
        $diaFechado = $this->diasFechados->find($id);

        if (! $diaFechado) {
            return false;
        }

        return $diaFechado->update($data);
    }

    public function delete(int $id): bool
    {
        $diaFechado = $this->diasFechados->find($id);

        if (! $diaFechado) {
            return false;
        }

        return $diaFechado->delete();
    }
}
