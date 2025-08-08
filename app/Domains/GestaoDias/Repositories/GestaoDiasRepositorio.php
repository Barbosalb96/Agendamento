<?php

namespace App\Domains\GestaoDias\Repositories;

use App\Domains\Agendamento\Entities\Agendamento;

interface GestaoDiasRepositorio
{
    public function listar(array $agendamento);

    //    public function cancelar(string $id, array $data): Agendamento;
    //
    //    public function buscar(array $filter);
}
