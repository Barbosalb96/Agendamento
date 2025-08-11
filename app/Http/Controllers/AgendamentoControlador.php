<?php

namespace App\Http\Controllers;

use App\Application\Agendamentos\Services\AgendamentoPorDia;
use App\Application\Agendamentos\Services\BuscarAgendamentoServico;
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
use Illuminate\Http\Request;

class AgendamentoControlador extends Controller
{
    public function __construct(
        private CriarAgendamentoServico    $criarAgendamentoServico,
        private ListarAgendamentoServico   $listarAgendamentoSerico,
        private CancelarAgendamentoServico $cancelarAgendamentoSerico,
        private AgendamentoPorDia          $agendamentoPorDia,
        private BuscarAgendamentoServico   $buscarAgendamentoServico,
    )
    {
    }

    /**
     * @OA\Get(
     *     path="/api/admin/agendamento",
     *     tags={"Agendamentos"},
     *     summary="Listar agendamentos",
     *     description="Lista todos os agendamentos com filtros e paginação",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=true,
     *
     *         @OA\Schema(type="integer", minimum=1, example=1),
     *         description="Número da página"
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=true,
     *
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15),
     *         description="Itens por página"
     *     ),
     *
     *     @OA\Parameter(
     *         name="data",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="2023-12-15"),
     *         description="Filtrar por data específica (Y-m-d, d-m-Y ou d/m/Y)"
     *     ),
     *
     *     @OA\Parameter(
     *         name="data_inicio",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="2023-12-01"),
     *         description="Filtrar a partir desta data (Y-m-d, d-m-Y ou d/m/Y)"
     *     ),
     *
     *     @OA\Parameter(
     *         name="data_fim",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="2023-12-31"),
     *         description="Filtrar até esta data (Y-m-d, d-m-Y ou d/m/Y)"
     *     ),
     *
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"ativo", "cancelado", "finalizado"}, example="ativo"),
     *         description="Filtrar por status do agendamento"
     *     ),
     *
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="integer", example=1),
     *         description="Filtrar por ID do usuário"
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de agendamentos",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(
     *
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
     *     description="Cria um novo agendamento. Requer dados pessoais completos, data/horário válidos e respeita regras de capacidade e disponibilidade.",
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","cpf","rg","telefone","nacionalidade","data","horario","quantidade"},
     *
     *             @OA\Property(
     *                 property="uuid",
     *                 type="string",
     *                 format="uuid",
     *                 description="UUID opcional para identificação única",
     *                 example="550e8400-e29b-41d4-a716-446655440000"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="E-mail único do solicitante",
     *                 example="joao.silva@email.com"
     *             ),
     *             @OA\Property(
     *                 property="cpf",
     *                 type="string",
     *                 pattern="^\d{11}$",
     *                 description="CPF único (11 dígitos, apenas números)",
     *                 example="12345678901"
     *             ),
     *             @OA\Property(
     *                 property="rg",
     *                 type="string",
     *                 maxLength=20,
     *                 description="RG do solicitante (máximo 20 caracteres)",
     *                 example="12.345.678-9"
     *             ),
     *             @OA\Property(
     *                 property="telefone",
     *                 type="string",
     *                 pattern="^\d{10,11}$",
     *                 description="Telefone único (10 ou 11 dígitos, apenas números)",
     *                 example="11987654321"
     *             ),
     *             @OA\Property(
     *                 property="nacionalidade",
     *                 type="string",
     *                 enum={"brasileiro","estrangeiro"},
     *                 description="Nacionalidade do solicitante",
     *                 example="brasileiro"
     *             ),
     *             @OA\Property(
     *                 property="nacionalidade_grupo",
     *                 type="string",
     *                 enum={"brasileiro","estrangeiro"},
     *                 description="Nacionalidade do solicitante",
     *                 example="brasileiro"
     *             ),
     *             @OA\Property(
     *                 property="deficiencia",
     *                 type="boolean",
     *                 description="Indica se a pessoa possui deficiência (opcional)",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 format="date",
     *                 description="Data do agendamento (mínimo: a partir de amanhã, não pode ser segunda-feira)",
     *                 example="2024-12-15"
     *             ),
     *             @OA\Property(
     *                 property="horario",
     *                 type="string",
     *                 pattern="^(09|10|11|12|13|14|15|16|17):00$",
     *                 description="Horário do agendamento (09:00 às 17:00, apenas horários cheios)",
     *                 example="14:00"
     *             ),
     *             @OA\Property(
     *                 property="grupo",
     *                 type="boolean",
     *                 description="Indica se é agendamento em grupo (grupos: 10-50 pessoas, individual: 1 pessoa)",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="quantidade",
     *                 type="integer",
     *                 minimum=1,
     *                 maximum=50,
     *                 description="Quantidade de pessoas (individual: 1, grupo: 10-50)",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="observacao",
     *                 type="string",
     *                 description="Observações adicionais (opcional)",
     *                 example="Necessário acessibilidade para cadeirante"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Agendamento criado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="mensagem", type="string", example="Agendamento realizado com sucesso")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="cpf", type="array", @OA\Items(type="string", example="Este CPF já está vinculado a um agendamento.")),
     *                 @OA\Property(property="data", type="array", @OA\Items(type="string", example="Não é possível agendar às segundas-feiras (bloqueio automático).")),
     *                 @OA\Property(property="horario", type="array", @OA\Items(type="string", example="Os horários disponíveis são apenas entre 09:00 e 17:00, em intervalos de 1 hora.")),
     *                 @OA\Property(property="quantidade", type="array", @OA\Items(type="string", example="Capacidade excedida: já existem 45 pessoas para 14:00. Restam apenas 5 vagas."))
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Token de autenticação inválido",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
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
     *     path="/api/admin/agendamento/find/{uuid}",
     *     tags={"Agendamentos"},
     *     summary="Visualizar agendamento",
     *     description="Visualiza os dados de um agendamento específico pelo UUID",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string", example="550e8400-e29b-41d4-a716-446655440000")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Dados do agendamento",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="uuid", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="data", type="string", example="15/12/2023"),
     *             @OA\Property(property="horario", type="string", example="14:00"),
     *             @OA\Property(property="grupo", type="boolean", example=false),
     *             @OA\Property(property="quantidade", type="integer", example=2),
     *             @OA\Property(property="observacao", type="string", example="Observação do agendamento")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Agendamento não encontrado",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Agendamento não encontrado")
     *         )
     *     )
     * )
     */
    public function show(string $uuid)
    {
        try {
            $agendamento = $this->buscarAgendamentoServico->executar($uuid);

            if (!$agendamento) {
                return response()->json([
                    'mensagem' => 'Agendamento não encontrado.',
                    'erro' => 'O agendamento com este UUID não existe no sistema.',
                    'codigo' => 404,
                ], 404);
            }

            return new AgendamentosResource($agendamento);
        } catch (Exception $exception) {
            return response()->json([
                'mensagem' => 'Erro interno do servidor.',
                'erro' => 'Ocorreu um erro inesperado ao buscar o agendamento.',
                'codigo' => 500,
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
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string", example="1")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Agendamento cancelado",
     *
     *         @OA\JsonContent(
     *
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

    /**
     * @OA\Get(
     *     path="/api/admin/agendamento/total-dia",
     *     tags={"Agendamentos"},
     *     summary="Total de agendamentos por dia",
     *     description="Retorna o total de agendamentos para um dia específico",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="data",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date", example="2024-12-15"),
     *         description="Data para consulta dos agendamentos (Y-m-d)"
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Total de agendamentos do dia",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", example="2024-12-15"),
     *             @OA\Property(property="total_agendamentos", type="integer", example=25),
     *             @OA\Property(property="total_pessoas", type="integer", example=35)
     *         )
     *     )
     * )
     */
    public function agendamentoDia(Request $request)
    {
        try {
            $data = $request->all();
            $resultado = $this->agendamentoPorDia->executar($data);

            return response()->json($resultado);
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
     *
     *     @OA\Parameter(
     *         name="data",
     *         in="query",
     *         required=true,
     *
     *         @OA\Schema(type="string", format="date", example="2023-12-15")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Disponibilidade de vagas",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="string", example="2023-12-15"),
     *             @OA\Property(property="bloqueado", type="boolean", example=false),
     *             @OA\Property(property="motivo", type="string", example=""),
     *             @OA\Property(property="horarios", type="array",
     *
     *                 @OA\Items(
     *
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
