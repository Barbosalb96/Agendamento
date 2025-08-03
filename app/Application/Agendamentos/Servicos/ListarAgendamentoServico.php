<?php

namespace App\Application\Agendamentos\Servicos;

use App\Domains\Agendamento\Repositories\ContratoAgendamentoRepositorio;

class ListarAgendamentoServico
{
    public function __construct(
        protected ContratoAgendamentoRepositorio $repositorio
    ) {}

    public function executar()
    {
       return $this->repositorio->buscar();
    }
}
