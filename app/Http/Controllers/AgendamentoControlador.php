<?php

namespace App\Http\Controllers;

use App\Application\Agendamentos\Servicos\CancelarAgendamentoServico;
use App\Application\Agendamentos\Servicos\CriarAgendamentoServico;
use App\Application\Agendamentos\Servicos\ListarAgendamentoServico;
use App\Domains\Agendamento\Entities\Agendamento;
use App\Http\Requests\StoreAgendamentoRequest;
use App\Http\Resources\AgendamentosResource;
use App\Models\DiasFechados;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgendamentoControlador extends Controller
{
    public function __construct(
        private CriarAgendamentoServico $criarAgendamentoServico,
        private ListarAgendamentoServico $listarAgendamentoSerico,
        private CancelarAgendamentoServico $cancelarAgendamentoSerico,
    ) {}

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

    public function destroy(string $id, Request $request)
    {
        try {
            $data = $request->all();
            $this->cancelarAgendamentoSerico->executar($id, $data);

            return response()->json(['mensagem' => 'Agendamento cancelado com sucesso']);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    // Controller
    public function vagasPorHorario(Request $request)
    {
        $data = Carbon::parse($request->query('data'))->startOfDay();

        // Bloqueios rápidos (segunda/dia fechado)
        if ($data->isMonday() || DiasFechados::whereDate('data', $data)->exists()) {
            return response()->json([
                'data' => $data->toDateString(),
                'bloqueado' => true,
                'motivo' => $data->isMonday() ? 'segunda-feira' : 'dia bloqueado manualmente',
                'horarios' => [],
            ]);
        }

        $horarios = [];
        foreach (range(9, 17) as $h) {
            $hora = sprintf('%02d:00', $h);
            $agendado = Agendamento::whereDate('data', $data)
                ->where('horario', $hora)
                ->sum('quantidade');

            $horarios[] = [
                'hora' => $hora,
                'vagas' => max(0, 50 - $agendado),
            ];
        }

        return response()->json([
            'data' => $data->toDateString(),
            'bloqueado' => false,
            'horarios' => $horarios,
        ]);
    }
}
