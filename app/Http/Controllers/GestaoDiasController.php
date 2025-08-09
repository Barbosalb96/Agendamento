<?php

namespace App\Http\Controllers;

use App\Application\GestaoDias\Servicos\AtualizarDia;
use App\Application\GestaoDias\Servicos\BuscarDia;
use App\Application\GestaoDias\Servicos\CriarDia;
use App\Application\GestaoDias\Servicos\DeletarDia;
use App\Application\GestaoDias\Servicos\ListasDias;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GestaoDiasController extends Controller
{
    public function __construct(
        private ListasDias $listasDias,
        private CriarDia $criarDia,
        private AtualizarDia $atualizarDia,
        private DeletarDia $deletarDia,
        private BuscarDia $buscarDia,
    ) {}

    /**
     * @OA\Get(
     *     path="/api/admin/gestao-dias",
     *     tags={"Gestão de Dias"},
     *     summary="Listar dias bloqueados",
     *     description="Lista todos os dias com bloqueios ou restrições",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="data",
     *         in="query",
     *         @OA\Schema(type="string", format="date", example="2023-12-15")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada de dias",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="data", type="string", format="date", example="2023-12-15"),
     *                     @OA\Property(property="tipo", type="string", example="bloqueio_total"),
     *                     @OA\Property(property="observacao", type="string", example="Feriado nacional")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filtros = [
            'data' => $request->get('data'),
            'per_page' => $request->get('per_page', 15),
            'page' => $request->get('page', 1),
        ];

        $dias = $this->listasDias->execute($filtros);

        return response()->json($dias);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/gestao-dias/{id}",
     *     tags={"Gestão de Dias"},
     *     summary="Buscar dia por ID",
     *     description="Busca um dia específico por ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do dia",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="data", type="string", format="date", example="2023-12-15"),
     *             @OA\Property(property="horario_inicio", type="string", example="08:00"),
     *             @OA\Property(property="horario_fim", type="string", example="18:00"),
     *             @OA\Property(property="tipo", type="string", example="bloqueio_total"),
     *             @OA\Property(property="observacao", type="string", example="Feriado nacional")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dia não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dia não encontrado")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $dia = $this->buscarDia->execute($id);

        if (! $dia) {
            return response()->json(['message' => 'Dia não encontrado'], 404);
        }

        return response()->json($dia);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/gestao-dias/store",
     *     tags={"Gestão de Dias"},
     *     summary="Criar novo dia bloqueado",
     *     description="Cria um novo registro de dia com bloqueio ou restrição",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"data","horario_inicio","horario_fim","tipo"},
     *             @OA\Property(property="data", type="string", format="date", example="2023-12-15"),
     *             @OA\Property(property="horario_inicio", type="string", example="08:00"),
     *             @OA\Property(property="horario_fim", type="string", example="18:00"),
     *             @OA\Property(property="tipo", type="string", enum={"bloqueio_total","bloqueio_parcial","manutencao","feriado"}, example="bloqueio_total"),
     *             @OA\Property(property="observacao", type="string", example="Feriado nacional")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Dia criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="data", type="string", format="date", example="2023-12-15")
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
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'data' => 'required|date',
            'horario_inicio' => 'required|date_format:H:i',
            'horario_fim' => 'required|date_format:H:i|after:horario_inicio',
            'tipo' => 'required|string|in:bloqueio_total,bloqueio_parcial,manutencao,feriado',
            'observacao' => 'nullable|string|max:500',
        ]);

        $dia = $this->criarDia->execute($validated);

        return response()->json($dia, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/gestao-dias/{id}",
     *     tags={"Gestão de Dias"},
     *     summary="Atualizar dia",
     *     description="Atualiza um registro de dia existente",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", format="date", example="2023-12-15"),
     *             @OA\Property(property="horario_inicio", type="string", example="08:00"),
     *             @OA\Property(property="horario_fim", type="string", example="18:00"),
     *             @OA\Property(property="tipo", type="string", enum={"bloqueio_total","bloqueio_parcial","manutencao","feriado"}, example="bloqueio_total"),
     *             @OA\Property(property="observacao", type="string", example="Feriado nacional")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dia atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dia atualizado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dia não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dia não encontrado")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'data' => 'sometimes|date',
            'horario_inicio' => 'sometimes|date_format:H:i',
            'horario_fim' => 'sometimes|date_format:H:i|after:horario_inicio',
            'tipo' => 'sometimes|string|in:bloqueio_total,bloqueio_parcial,manutencao,feriado',
            'observacao' => 'nullable|string|max:500',
        ]);
        $updated = $this->atualizarDia->execute($validated, $id);

        if (! $updated) {
            return response()->json(['message' => 'Dia não encontrado'], 404);
        }

        return response()->json(['message' => 'Dia atualizado com sucesso']);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/gestao-dias/{id}",
     *     tags={"Gestão de Dias"},
     *     summary="Excluir dia",
     *     description="Exclui um registro de dia",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dia excluído com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dia excluído com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dia não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dia não encontrado")
     *         )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->deletarDia->execute($id);

        if (! $deleted) {
            return response()->json(['message' => 'Dia não encontrado'], 404);
        }

        return response()->json(['message' => 'Dia excluído com sucesso']);
    }
}
