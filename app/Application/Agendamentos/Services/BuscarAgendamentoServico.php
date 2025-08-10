<?php

namespace App\Application\Agendamentos\Services;

use App\Domains\Agendamento\Repositories\ContratoAgendamentoRepositorio;

class BuscarAgendamentoServico
{
    public function __construct(
        protected ContratoAgendamentoRepositorio $repositorio
    ) {}

    public function executar(string $id)
    {
        return $this->repositorio->buscarPorId($id);
    }
}
