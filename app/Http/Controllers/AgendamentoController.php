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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgendamentoController extends Controller
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
     *     security={{"sanctum":{"d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"}}},
     *
     *     @OA\Parameter(
     *         name="X-Security-Hash",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", example="d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"),
     *         description="Hash de segurança obrigatório para acesso às rotas protegidas"
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, example=1, default=1),
     *         description="Número da página (padrão: 1)"
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15, default=15),
     *         description="Itens por página (padrão: 15)"
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
     *     @OA\Response(
     *         response=200,
     *         description="Lista de agendamentos",
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="uuid", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                 @OA\Property(property="nome", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", example="joao.silva@email.com"),
     *                 @OA\Property(property="cpf", type="string", example="12345678901"),
     *                 @OA\Property(property="telefone", type="string", example="11987654321"),
     *                 @OA\Property(property="data", type="string", example="15/12/2023"),
     *                 @OA\Property(property="horario", type="string", example="14:00"),
     *                 @OA\Property(property="grupo", type="boolean", example=false),
     *                 @OA\Property(property="quantidade", type="integer", example=2),
     *                 @OA\Property(property="status", type="string", example="ativo"),
     *                 @OA\Property(property="observacao", type="string", example="Observação do agendamento"),
     *                 @OA\Property(property="horario_comparecimento", type="string", example="14:05", description="Horário de comparecimento registrado no QR Code (formato H:i)"),
     *                 @OA\Property(property="horario_entrada", type="string", example="14:10", description="Horário de entrada confirmada (formato H:i)"),
     *                 @OA\Property(property="created_at", type="string", example="2023-12-10T10:30:00Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-12-10T10:30:00Z")
     *             )),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="total", type="integer", example=150),
     *             @OA\Property(property="last_page", type="integer", example=10)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Token de autenticação inválido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação nos parâmetros",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="page", type="array", @OA\Items(type="string", example="O campo page deve ser maior que 0.")),
     *                 @OA\Property(property="per_page", type="array", @OA\Items(type="string", example="O campo per_page deve estar entre 1 e 100."))
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
     *     path="/api/agendar",
     *     tags={"Agendamentos"},
     *     summary="Criar agendamento",
     *     description="Cria um novo agendamento. Requer dados pessoais completos, data/horário válidos e respeita regras de capacidade e disponibilidade.",
     *     security={{"sanctum":{"d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"nome","email","cpf","rg","telefone","nacionalidade","data","horario","quantidade"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="E-mail único do solicitante",
     *                 example="joao.silva@email.com"
     *             ),
     *             @OA\Property(
     *                 property="nome",
     *                 type="string",
     *                 description="Nome completo do solicitante",
     *                 example="João Silva Santos"
     *             ),
     *             @OA\Property(
     *                 property="cpf",
     *                 type="string",
     *                 pattern="^\d{11}$",
     *                 description="CPF único (11 dígitos, apenas números)",
     *                 example="12345678901"
     *             ),
     *             @OA\Property(
     *                  property="documento",
     *                  type="string",
     *                  description="Documento ",
     *                  example="12345678901"
     *              ),
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
     *     security={{"sanctum":{"d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"}}},
     *
     *     @OA\Parameter(
     *         name="X-Security-Hash",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", example="d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"),
     *         description="Hash de segurança obrigatório para acesso às rotas protegidas"
     *     ),
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
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="uuid", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="nome", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", example="joao.silva@email.com"),
     *             @OA\Property(property="cpf", type="string", example="12345678901"),
     *             @OA\Property(property="rg", type="string", example="12.345.678-9"),
     *             @OA\Property(property="telefone", type="string", example="11987654321"),
     *             @OA\Property(property="nacionalidade", type="string", example="brasileiro"),
     *             @OA\Property(property="data", type="string", example="15/12/2023"),
     *             @OA\Property(property="horario", type="string", example="14:00"),
     *             @OA\Property(property="grupo", type="boolean", example=false),
     *             @OA\Property(property="quantidade", type="integer", example=2),
     *             @OA\Property(property="status", type="string", example="ativo"),
     *             @OA\Property(property="observacao", type="string", example="Observação do agendamento"),
     *             @OA\Property(property="deficiencia", type="boolean", example=false),
     *             @OA\Property(property="horario_comparecimento", type="string", example="14:05", description="Horário de comparecimento registrado no QR Code (formato H:i)"),
     *             @OA\Property(property="horario_entrada", type="string", example="14:10", description="Horário de entrada confirmada (formato H:i)"),
     *             @OA\Property(property="created_at", type="string", example="2023-12-10T10:30:00Z"),
     *             @OA\Property(property="updated_at", type="string", example="2023-12-10T10:30:00Z")
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
     *     security={{"sanctum":{"d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"}}},
     *
     *     @OA\Parameter(
     *         name="X-Security-Hash",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", example="d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"),
     *         description="Hash de segurança obrigatório para acesso às rotas protegidas"
     *     ),
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
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Agendamento não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Agendamento não encontrado")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Token de autenticação inválido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error")
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
     *     security={{"sanctum":{"d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"}}},
     *
     *     @OA\Parameter(
     *         name="X-Security-Hash",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", example="d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"),
     *         description="Hash de segurança obrigatório para acesso às rotas protegidas"
     *     ),
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

    /**
     * @OA\Get(
     *     path="/api/vagas-por-horario",
     *     tags={"Agendamentos"},
     *     summary="Consultar vagas por horário",
     *     description="Consulta a disponibilidade de vagas em uma data específica",
     *     security={{"sanctum":{"d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"}}},
     *
     *     @OA\Parameter(
     *         name="X-Security-Hash",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", example="d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"),
     *         description="Hash de segurança obrigatório para acesso às rotas protegidas"
     *     ),
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

    /**
     * @OA\Get(
     *     path="/api/admin/agendamento/relatorio",
     *     tags={"Agendamentos"},
     *     summary="Relatório de agendamentos",
     *     description="Gera relatório com quantidade de agendamentos por dia, mês e distribuição por horário",
     *     security={{"sanctum":{"d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"}}},
     *
     *     @OA\Parameter(
     *         name="X-Security-Hash",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", example="d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"),
     *         description="Hash de segurança obrigatório para acesso às rotas protegidas"
     *     ),
     *
     *     @OA\Parameter(
     *         name="data",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date", example="2024-12-15"),
     *         description="Data de referência para o relatório (Y-m-d)"
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Relatório gerado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data_referencia", type="string", example="2024-12-15"),
     *             @OA\Property(property="quantidade_dia", type="integer", example=75),
     *             @OA\Property(property="quantidade_mes", type="integer", example=1520),
     *             @OA\Property(property="grafico", type="object",
     *                 @OA\Property(property="labels", type="array", @OA\Items(type="string", example="09:00")),
     *                 @OA\Property(property="values", type="array", @OA\Items(type="integer", example=5)),
     *                 @OA\Property(property="raw", type="array", @OA\Items(
     *                     @OA\Property(property="horario", type="string", example="09:00"),
     *                     @OA\Property(property="total", type="integer", example=5)
     *                 ))
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(type="string", example="O campo data é obrigatório."))
     *             )
     *         )
     *     )
     * )
     */
    public function relatorio(Request $request)
    {
        $validated = $request->validate([
            'data' => ['required', 'date'],
        ]);

        $dt = Carbon::parse($validated['data'])->startOfDay();

        $quantidadeDia = Agendamento::query()
            ->whereDate('data', $dt)
            ->sum('quantidade');

        $quantidadeMes = Agendamento::query()
            ->whereYear('data', $dt->year)
            ->whereMonth('data', $dt->month)
            ->sum('quantidade');

        $dt = Carbon::parse($validated['data'])->startOfDay();

        $porHorario = Agendamento::query()
            ->selectRaw("DATE_FORMAT(horario, '%H:%i') as horario, SUM(quantidade) as total")
            ->whereDate('data', $dt)
            ->groupBy('horario')
            ->orderBy('horario')
            ->get();

        return response()->json([
            'data_referencia' => $dt->toDateString(),
            'quantidade_dia' => (int)$quantidadeDia,
            'quantidade_mes' => (int)$quantidadeMes,
            "grafico" => [
                'labels' => $porHorario->pluck('horario'),
                'values' => $porHorario->pluck('total')->map(fn($v) => (int)$v),
                'raw' => $porHorario,
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/validar-qrcode/{uuid}",
     *     tags={"QR Code"},
     *     summary="Validar QR Code",
     *     description="Valida um QR Code de agendamento e registra o status da chegada baseado no horário",
     *
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", example="550e8400-e29b-41d4-a716-446655440000")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="QR Code válido - Visitante chegou dentro do prazo",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensagem", type="string", example="QR Code válido"),
     *             @OA\Property(property="status", type="string", enum={"verde","laranja","vermelho"}, example="verde", description="Status da chegada: verde=pontual, laranja=atraso aceitável, vermelho=muito atrasado"),
     *             @OA\Property(property="detalhe_status", type="string", example="Chegou no horário ou antes do início."),
     *             @OA\Property(property="marcos", type="object",
     *                 @OA\Property(property="chegada", type="string", example="15/12/2023 13:45", description="Horário atual da validação"),
     *                 @OA\Property(property="inicio", type="string", example="15/12/2023 14:00", description="Horário de início da visita"),
     *                 @OA\Property(property="fim", type="string", example="15/12/2023 15:00", description="Horário de término da visita"),
     *                 @OA\Property(property="limite_vermelho", type="string", example="15/12/2023 14:40", description="Limite para status vermelho (20min antes do fim)")
     *             ),
     *             @OA\Property(property="agendamento", type="object",
     *                 @OA\Property(property="data", type="string", example="15/12/2023"),
     *                 @OA\Property(property="horario", type="string", example="14:00"),
     *                 @OA\Property(property="grupo", type="string", example="Individual", description="Individual ou Grupo"),
     *                 @OA\Property(property="quantidade", type="integer", example=1),
     *                 @OA\Property(property="cpf", type="string", example="12345678901"),
     *                 @OA\Property(property="nome", type="string", example="João Silva"),
     *                 @OA\Property(property="observacao", type="string", example="Necessário acessibilidade"),
     *                 @OA\Property(property="status_reg", type="string", example="verde", description="Status registrado no agendamento")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="QR Code não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensagem", type="string", example="QR Code inválido ou agendamento não encontrado")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="QR Code expirado - Horário da visita já passou",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensagem", type="string", example="QR Code inválido: horário expirado (visita já encerrada).")
     *         )
     *     )
     * )
     */
    public function validarQRCode(string $uuid): JsonResponse
    {
        $tz = 'America/Fortaleza';

        $agendamento = Agendamento::where('uuid', $uuid)->first();

        if (!$agendamento) {
            return response()->json([
                'mensagem' => 'QR Code inválido ou agendamento não encontrado'
            ], 404);
        }

        $data = $agendamento->data instanceof Carbon
            ? $agendamento->data->copy()->timezone($tz)->startOfDay()
            : Carbon::parse($agendamento->data, $tz)->startOfDay();

        $horaInicio = $agendamento->horario instanceof Carbon
            ? $agendamento->horario->copy()->timezone($tz)->format('H:i')
            : substr((string)$agendamento->horario, 0, 5);

        $inicio = Carbon::parse($data->format('Y-m-d') . ' ' . $horaInicio, $tz);
        $fim = $inicio->copy()->addMinutes(60);

        $chegada = Carbon::now($tz);

        if ($chegada->greaterThan($fim)) {
            return response()->json([
                'mensagem' => 'QR Code inválido: horário expirado (visita já encerrada).'
            ], 422);
        }

        $limiteVermelho = $fim->copy()->subMinutes(20);

        if ($chegada->lessThanOrEqualTo($inicio)) {
            $status = 'verde';
            $detalhe = 'Chegou no horário ou antes do início.';
        } elseif ($chegada->lessThanOrEqualTo($limiteVermelho)) {
            $status = 'laranja';
            $detalhe = 'Chegou após o início, com tempo hábil para a visita.';
        } else {
            $status = 'vermelho';
            $detalhe = 'Chegou faltando menos de 20 minutos para o término (fora do limite).';
        }

        $agendamento->status = $status;
        $agendamento->horario_comparecimento = Carbon::now()->format('H:i');
        $agendamento->save();


        return response()->json([
            'mensagem' => 'QR Code válido',
            'status' => $status,
            'detalhe_status' => $detalhe,
            'marcos' => [
                'chegada' => $chegada->format('d/m/Y H:i'),
                'inicio' => $inicio->format('d/m/Y H:i'),
                'fim' => $fim->format('d/m/Y H:i'),
                'limite_vermelho' => $limiteVermelho->format('d/m/Y H:i'),
            ],
            'agendamento' => [
                'data' => $agendamento->data_formatada ?? Carbon::parse($agendamento->data, $tz)->format('d/m/Y'),
                'horario' => $agendamento->horario_formatado ?? $horaInicio,
                'grupo' => $agendamento->grupo ? 'Grupo' : 'Individual',
                'quantidade' => $agendamento->quantidade,
                'cpf' => $agendamento->cpf,
                'nome' => $agendamento->nome,
                'observacao' => $agendamento->observacao,
                'status_reg' => $agendamento->status ?? null,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/agendamento/confirmar",
     *     tags={"Agendamentos"},
     *     summary="Confirmar entrada do agendamento",
     *     description="Confirma a entrada do visitante registrando o horário de entrada",
     *     security={{"sanctum":{"d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"}}},
     *
     *     @OA\Parameter(
     *         name="X-Security-Hash",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", example="d918deebb121aa0d437875f57b39cdf96dd8f85873d899b921e2706daddd4904"),
     *         description="Hash de segurança obrigatório para acesso às rotas protegidas"
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"uuid"},
     *             @OA\Property(property="uuid", type="string", example="550e8400-e29b-41d4-a716-446655440000", description="UUID do agendamento")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Entrada confirmada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", example="Confirmado com sucesso")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Agendamento não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Agendamento não encontrado")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Token de autenticação inválido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function confirmar(Request $request)
    {
        $agendamento = Agendamento::where('uuid', $request->input('uuid'))->first();
        $agendamento->horario_entrada = Carbon::now()->format('H:i');
        $agendamento->save();

        return response()->json(["data" => "Confirmado com sucesso"]);
    }
}
