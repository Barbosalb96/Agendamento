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

    /**
     * @OA\Get(
     *     path="/api/admin/agendamento",
     *     tags={"Agendamentos"},
     *     summary="Listar agendamentos",
     *     description="Lista todos os agendamentos",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de agendamentos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="data", type="string", format="date", example="2023-12-15"),
     *                 @OA\Property(property="horario", type="string", example="14:00:00"),
     *                 @OA\Property(property="quantidade", type="integer", example=2),
     *                 @OA\Property(property="observacao", type="string", example="Observação")
     *             )
     *         )
     *     )
     * )
     */
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
    /**
     * @OA\Post(
     *     path="/api/admin/agendamento",
     *     tags={"Agendamentos"},
     *     summary="Criar agendamento",
     *     description="Cria um novo agendamento",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"data","horario","quantidade"},
     *             @OA\Property(property="data", type="string", format="date", example="2023-12-15"),
     *             @OA\Property(property="horario", type="string", example="14:00"),
     *             @OA\Property(property="quantidade", type="integer", example=2),
     *             @OA\Property(property="observacao", type="string", example="Observação do agendamento"),
     *             @OA\Property(property="grupo", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agendamento criado",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensagem", type="string", example="Agendamento realizado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dados inválidos")
     *         )
     *     )
     * )
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

    /**
     * @OA\Delete(
     *     path="/api/admin/agendamento/{id}",
     *     tags={"Agendamentos"},
     *     summary="Cancelar agendamento",
     *     description="Cancela um agendamento existente",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", example="1")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agendamento cancelado",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensagem", type="string", example="Agendamento cancelado com sucesso")
     *         )
     *     )
     * )
     */
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
    /**
     * @OA\Get(
     *     path="/api/admin/agendamento/vagas-por-horario",
     *     tags={"Agendamentos"},
     *     summary="Consultar vagas por horário",
     *     description="Consulta a disponibilidade de vagas em uma data específica",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="data",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date", example="2023-12-15")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Disponibilidade de vagas",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", example="2023-12-15"),
     *             @OA\Property(property="bloqueado", type="boolean", example=false),
     *             @OA\Property(property="motivo", type="string", example=""),
     *             @OA\Property(property="horarios", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="hora", type="string", example="09:00"),
     *                     @OA\Property(property="vagas", type="integer", example=48)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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
