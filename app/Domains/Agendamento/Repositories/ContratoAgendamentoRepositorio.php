<?php

namespace App\Domains\Agendamento\Repositories;

use App\Domains\Agendamento\Entities\Agendamento;

interface ContratoAgendamentoRepositorio
{
    public function salvar(array $agendamento);

    public function cancelar(string $id): Agendamento;

    public function buscar(array $filter);
}
