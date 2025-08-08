<?php

namespace App\Application\Agendamentos\Servicos;

use App\Domains\Agendamento\Repositories\GestaoDiasRepositorio;

class ListarAgendamentoServico
{
    public function __construct(
        protected GestaoDiasRepositorio $repositorio
    ) {}

    public function executar(array $filter)
    {
        return $this->repositorio->buscar($filter);
    }
}
