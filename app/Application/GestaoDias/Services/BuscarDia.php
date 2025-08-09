<?php

namespace App\Application\GestaoDias\Services;

use App\Domains\GestaoDias\Repositories\GestaoDiasRepositorio;

class BuscarDia
{
    public function __construct(
        private GestaoDiasRepositorio $gestaoDias,
    ) {}

    public function execute(int $id)
    {
        return $this->gestaoDias->buscar($id);
    }
}
