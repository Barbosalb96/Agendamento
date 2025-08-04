<?php

namespace App\Http\Controllers;

use App\Application\Agendamentos\Servicos\CriarAgendamentoServico;
use App\Application\Agendamentos\Servicos\ListarAgendamentoServico;
use App\Http\Requests\StoreAgendamentoRequest;
use App\Http\Resources\AgendamentosResource;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgendamentoControlador extends Controller
{
    public function __construct(
        private CriarAgendamentoServico  $criarAgendamentoServico,
        private ListarAgendamentoServico $listarAgendamentoSerico
    )
    {
    }

    public function index(Request $request)
    {
        $response = $this->listarAgendamentoSerico->executar(
            $request->all()
        );

        return AgendamentosResource::collection($response);
    }

    /**
     * Exibe a lista de agendamentos do usuário autenticado.
     *
     * @return JsonResponse
     */
    public function agendar(StoreAgendamentoRequest $storeAgendamentoRequest)
    {
        try {
            $this->criarAgendamentoServico->executar(
                $storeAgendamentoRequest->validated()
            );
            return response()->json(['mensagem' => 'Agendamento realizado com sucesso']);
        } catch (Exception $exception) {
            throw $exception;
        }

    }
}
