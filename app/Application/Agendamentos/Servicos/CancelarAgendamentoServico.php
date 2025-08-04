<?php

namespace App\Application\Agendamentos\Servicos;


use App\Domains\Agendamento\Repositories\ContratoAgendamentoRepositorio;
use App\Mail\CancelarAgendamentoMail;
use Illuminate\Support\Facades\Mail;

class CancelarAgendamentoServico
{
    public function __construct(
        protected ContratoAgendamentoRepositorio $repositorio
    ) {}

    public function executar(string $id): void
    {
      $agendamento =  $this->repositorio->cancelar($id);

      Mail::to($agendamento->user->email)->send(
          new CancelarAgendamentoMail($agendamento)
      );
    }
}
