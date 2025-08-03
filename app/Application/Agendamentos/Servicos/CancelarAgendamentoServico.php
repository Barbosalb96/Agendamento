<?php

namespace App\Application\Agendamentos\Servicos;

use App\Dominio\Agendamentos\Repositorios\ContratoAgendamentoRepositorio;

class CancelarAgendamentoServico
{
    public function __construct(
        protected ContratoAgendamentoRepositorio $repositorio
    ) {}

    public function cancelar(string $id): void
    {
        $this->repositorio->cancelar($id);
    }
}
