<?php

namespace App\Domains\GestaoDias\Repositories;

interface GestaoDiasRepositorio
{
    public function listar(array $filtro);

    public function buscar(int $id);

    public function create(array $data);

    public function update(array $data, int $id): bool;

    public function delete(int $id): bool;
}
