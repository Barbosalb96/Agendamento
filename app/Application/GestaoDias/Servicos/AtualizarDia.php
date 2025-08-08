<?php

namespace App\Application\GestaoDias\Servicos;

use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentGestaoDias;

class AtualizarDia
{
    public function __construct(
        private EloquentGestaoDias $gestaoDias,
    ) {}

    public function execute(array $data, int $id): bool
    {
        return $this->gestaoDias->update($data, $id);
    }
}
