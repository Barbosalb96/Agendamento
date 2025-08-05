<?php

namespace App\Application\Agendamentos\Servicos;

use App\Domains\Agendamento\Repositories\ContratoAgendamentoRepositorio;
use App\Mail\AgendamentoMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
            Mail::to($agendamento->user->email)->send(
                new AgendamentoMail($agendamento)
            );
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

    }
}
