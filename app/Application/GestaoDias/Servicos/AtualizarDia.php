<?php

namespace App\Application\GestaoDias\Servicos;

use App\Domains\GestaoDias\Repositories\GestaoDiasRepositorio;

class AtualizarDia
{
    public function __construct(
        private GestaoDiasRepositorio $gestaoDias,
    ) {}

    public function execute(array $data, int $id): bool
    {
        return $this->gestaoDias->update($data, $id);
    }
}
