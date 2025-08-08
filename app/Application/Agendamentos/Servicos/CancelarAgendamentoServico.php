<?php

namespace App\Application\Agendamentos\Servicos;

use App\Domains\Agendamento\Repositories\GestaoDiasRepositorio;
use App\Jobs\MailDispatchDefault;

class CancelarAgendamentoServico
{
    public function __construct(
        protected GestaoDiasRepositorio $repositorio
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
