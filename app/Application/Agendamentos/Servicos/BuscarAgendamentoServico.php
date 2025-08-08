<?php

namespace App\Application\Agendamentos\Servicos;

use App\Domains\Agendamento\Repositories\GestaoDiasRepositorio;

class BuscarAgendamentoServico
{
    public function __construct(
        protected GestaoDiasRepositorio $repositorio
    ) {}

    public function executar(string $id)
    {
        return $this->repositorio->buscar($id);
    }
}
