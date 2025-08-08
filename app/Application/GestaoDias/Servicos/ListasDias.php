<?php

namespace App\Application\GestaoDias\Servicos;

use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentGestaoDias;

class ListasDias
{
    public function __construct(
        private EloquentGestaoDias $gestaoDias,
    ) {}

    public function execute(array $data)
    {
        return $this->gestaoDias->listar($data);
    }
}
