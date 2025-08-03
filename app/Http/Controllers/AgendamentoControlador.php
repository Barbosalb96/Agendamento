<?php

namespace App\Http\Controllers;

use App\Application\Agendamentos\Servicos\CriarAgendamentoServico;
use App\Application\Agendamentos\Servicos\ListarAgendamentoServico;
use App\Http\Requests\StoreAgendamentoRequest;
use App\Http\Resources\AgendamentosResource;
use Carbon\Carbon;

class AgendamentoControlador extends Controller
{
    public function __construct(
        private CriarAgendamentoServico  $criarAgendamentoServico,
        private ListarAgendamentoServico $listarAgendamentoSerico
    )
    {
    }

    public function index()
    {
        $response = $this->listarAgendamentoSerico->executar();

        return AgendamentosResource::collection($response);

    }

    /**
     * Exibe a lista de agendamentos do usuário autenticado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function agendar(StoreAgendamentoRequest $agendarRequest)
    {
        try {
            $this->criarAgendamentoServico->executar(
                $agendarRequest->validated()
            );
            return response()->json(['mensagem' => 'Agendamento realizado com sucesso']);
        } catch (\Exception $exception) {
            throw $exception;
        }

    }
}
