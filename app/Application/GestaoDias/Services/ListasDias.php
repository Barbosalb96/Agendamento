<?php

namespace App\Application\GestaoDias\Services;

use App\Domains\GestaoDias\Repositories\GestaoDiasRepositorio;

class ListasDias
{
    public function __construct(
        private GestaoDiasRepositorio $gestaoDias,
    ) {}

    public function execute(array $data)
    {
        return $this->gestaoDias->listar($data);
    }
}
