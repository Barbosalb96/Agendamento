<?php

namespace App\Application\GestaoDias\Servicos;

use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentGestaoDias;

class CriarDia
{
    public function __construct(
        private EloquentGestaoDias $gestaoDias,
    ) {}

    public function execute(array $data)
    {
        return $this->gestaoDias->create($data);
    }
}
