<?php

namespace App\Application\Agendamentos\Services;

use App\Domains\Agendamento\Repositories\ContratoAgendamentoRepositorio;
use App\Jobs\MailDispatchDefault;

class CancelarAgendamentoServico
{
    public function __construct(
        protected ContratoAgendamentoRepositorio $repositorio
    ) {}

    public function executar(string $id, array $data): void
    {
        $agendamento = $this->repositorio->cancelar($id, $data);

        dispatch(new MailDispatchDefault(
            'Cancelamento de Agendamento - Governo do Maranhão',
            [
                'agendamento' => $this->agendamento,
            ],
            'cancelamento_agendamento',
            $agendamento->user->email
        ));
    }
}
