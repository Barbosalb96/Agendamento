<?php

namespace App\Application\GestaoDias\Servicos;

use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentGestaoDias;

class BuscarDia
{
    public function __construct(
        private EloquentGestaoDias $gestaoDias,
    ) {}

    public function execute(int $id)
    {
        return $this->gestaoDias->buscar($id);
    }
}
