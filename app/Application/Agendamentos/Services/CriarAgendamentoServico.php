<?php

namespace App\Application\Agendamentos\Services;

use App\Domains\Agendamento\Entities\Agendamento;
use App\Domains\Agendamento\Repositories\ContratoAgendamentoRepositorio;
use App\Notifications\Notifications;
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

            $totalAgendado = Agendamento::whereDate('data', $agendamento['data'])
                ->where('horario', $agendamento['horario'])
                ->sum('quantidade');

            if ($totalAgendado >= 50) {
                throw new \Exception('Limite de agendamento excedido');
            }

            $agendamento = $this->repositorio->salvar($agendamento);

            Notifications::agendamento($agendamento);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

    }
}
