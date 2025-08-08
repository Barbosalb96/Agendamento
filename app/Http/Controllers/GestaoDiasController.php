<?php

namespace App\Http\Controllers;

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

    public function show(int $id): JsonResponse
    {
        $dia = $this->buscarDia->execute($id);

        if (! $dia) {
            return response()->json(['message' => 'Dia não encontrado'], 404);
        }

        return response()->json($dia);
    }

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

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->deletarDia->execute($id);

        if (! $deleted) {
            return response()->json(['message' => 'Dia não encontrado'], 404);
        }

        return response()->json(['message' => 'Dia excluído com sucesso']);
    }
}
