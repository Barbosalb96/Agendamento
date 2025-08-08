<?php

namespace App\Application\GestaoDias\Servicos;

use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentGestaoDias;

class DeletarDia
{
    public function __construct(
        private EloquentGestaoDias $gestaoDias,
    ) {}

    public function execute(int $id): bool
    {
        return $this->gestaoDias->delete($id);
    }
}
