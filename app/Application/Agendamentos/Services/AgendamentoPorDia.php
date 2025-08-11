<?php

namespace App\Application\Agendamentos\Services;

use App\Domains\Agendamento\Repositories\ContratoAgendamentoRepositorio;

class AgendamentoPorDia
{
    public function __construct(
        protected ContratoAgendamentoRepositorio $repositorio
    )
    {
    }

    public function executar(array $data)
    {
        return $this->repositorio->quantidadePorDia($data);
    }
}
