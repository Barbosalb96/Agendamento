<?php

namespace App\Application\Agendamentos\Servicos;

use App\Domains\Agendamento\Repositories\ContratoAgendamentoRepositorio;
use App\Jobs\MailDispatchDefault;
use Illuminate\Support\Facades\DB;

class CriarAgendamentoServico
{
    public function __construct(
        protected ContratoAgendamentoRepositorio $repositorio
    ) {}

    public function executar(array $agendamento): void
    {
        try {
            DB::beginTransaction();
            $agendamento = $this->repositorio->salvar($agendamento);

            dispatch(new MailDispatchDefault(
                'Agendamento - Governo do Maranhão',
                [
                    'agendamento' => $agendamento,
                    'usuario' => $agendamento->user,
                ],
                'agendamento',
                $agendamento->user->email,
            ));

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

    }
}
