<?php

namespace App\Application\GestaoDias\Services;

use App\Domains\GestaoDias\Repositories\GestaoDiasRepositorio;

class DeletarDia
{
    public function __construct(
        private GestaoDiasRepositorio $gestaoDias,
    ) {}

    public function execute(int $id): bool
    {
        return $this->gestaoDias->delete($id);
    }
}
