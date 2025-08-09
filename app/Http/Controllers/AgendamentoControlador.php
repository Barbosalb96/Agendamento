<?php

namespace App\Http\Controllers;

use App\Application\Agendamentos\Services\CancelarAgendamentoServico;
use App\Application\Agendamentos\Services\CriarAgendamentoServico;
use App\Application\Agendamentos\Services\ListarAgendamentoServico;
use App\Domains\Agendamento\Entities\Agendamento;
use App\Http\Requests\FilterAgendamentoRequest;
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
     *     description="Lista todos os agendamentos com filtros e paginação",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer", minimum=1, example=1),
     *         description="Número da página"
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15),
     *         description="Itens por página"
     *     ),
     *     @OA\Parameter(
     *         name="data",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="2023-12-15"),
     *         description="Filtrar por data específica (Y-m-d, d-m-Y ou d/m/Y)"
     *     ),
     *     @OA\Parameter(
     *         name="data_inicio",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="2023-12-01"),
     *         description="Filtrar a partir desta data (Y-m-d, d-m-Y ou d/m/Y)"
     *     ),
     *     @OA\Parameter(
     *         name="data_fim",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="2023-12-31"),
     *         description="Filtrar até esta data (Y-m-d, d-m-Y ou d/m/Y)"
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"ativo", "cancelado", "finalizado"}, example="ativo"),
     *         description="Filtrar por status do agendamento"
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", example=1),
     *         description="Filtrar por ID do usuário"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de agendamentos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="uuid", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                 @OA\Property(property="data", type="string", example="15/12/2023"),
     *                 @OA\Property(property="horario", type="string", example="14:00"),
     *                 @OA\Property(property="grupo", type="boolean", example=false),
     *                 @OA\Property(property="quantidade", type="integer", example=2),
     *                 @OA\Property(property="observacao", type="string", example="Observação do agendamento")
     *             )
     *         )
     *     )
     * )
     */
    public function index(FilterAgendamentoRequest $filterAgendamentoRequest)
    {
        $response = $this->listarAgendamentoSerico->executar(
            $filterAgendamentoRequest->validated()
        );

        return AgendamentosResource::collection($response);
    }

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
     * @OA\Get(
     *     path="/api/admin/agendamento/{uuid}",
     *     tags={"Agendamentos"},
     *     summary="Visualizar agendamento",
     *     description="Visualiza os dados de um agendamento específico pelo UUID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", example="550e8400-e29b-41d4-a716-446655440000")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do agendamento",
     *         @OA\JsonContent(
     *             @OA\Property(property="uuid", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="data", type="string", example="15/12/2023"),
     *             @OA\Property(property="horario", type="string", example="14:00"),
     *             @OA\Property(property="grupo", type="boolean", example=false),
     *             @OA\Property(property="quantidade", type="integer", example=2),
     *             @OA\Property(property="observacao", type="string", example="Observação do agendamento")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Agendamento não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Agendamento não encontrado")
     *         )
     *     )
     * )
     */
    public function show(string $uuid)
    {
        try {
            $agendamento = Agendamento::where('uuid', $uuid)->first();

            if (!$agendamento) {
                return response()->json([
                    'mensagem' => 'Agendamento não encontrado.',
                    'erro' => 'O agendamento com este UUID não existe no sistema.',
                    'codigo' => 404
                ], 404);
            }

            return new AgendamentosResource($agendamento);
        } catch (Exception $exception) {
            return response()->json([
                'mensagem' => 'Erro interno do servidor.',
                'erro' => 'Ocorreu um erro inesperado ao buscar o agendamento.',
                'codigo' => 500
            ], 500);
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
