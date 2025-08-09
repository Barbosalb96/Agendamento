<?php

namespace Tests\Unit;

use App\Application\Agendamentos\Servicos\CriarAgendamentoServico;
use App\Domains\Agendamento\Entities\Agendamento;
use App\Domains\Agendamento\Exceptions\ConflitoDeHorarioException;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentAgendamentoRepositorio;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUsuarioRepositorio;
use App\Models\DiasFechados;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CriarAgendamentoServicoTest extends TestCase
{
    use RefreshDatabase;

    private CriarAgendamentoServico $servico;
    private User $usuario;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->servico = new CriarAgendamentoServico(
            new EloquentAgendamentoRepositorio(),
            new EloquentUsuarioRepositorio()
        );
        
        $this->usuario = User::factory()->create();
    }

    public function test_criar_agendamento_com_dados_validos()
    {
        $dados = [
            'user_id' => $this->usuario->id,
            'data' => Carbon::tomorrow()->addDays(1)->format('Y-m-d'), // Evita segunda-feira
            'horario' => '14:00',
            'quantidade' => 2,
            'grupo' => false,
            'observacao' => 'Agendamento teste'
        ];

        $resultado = $this->servico->executar($dados);

        $this->assertDatabaseHas('agendamentos', [
            'user_id' => $this->usuario->id,
            'data' => $dados['data'],
            'horario' => $dados['horario'] . ':00',
            'quantidade' => $dados['quantidade']
        ]);
    }

    public function test_nao_criar_agendamento_em_segunda_feira()
    {
        $proximaSegunda = Carbon::now()->next(Carbon::MONDAY);
        
        $dados = [
            'user_id' => $this->usuario->id,
            'data' => $proximaSegunda->format('Y-m-d'),
            'horario' => '14:00',
            'quantidade' => 1,
            'grupo' => false
        ];

        $this->expectException(\Exception::class);
        $this->servico->executar($dados);
    }

    public function test_nao_criar_agendamento_em_dia_fechado()
    {
        $dataFechada = Carbon::tomorrow()->addDays(2);
        
        DiasFechados::factory()->create([
            'data' => $dataFechada->format('Y-m-d'),
            'tipo' => 'bloqueio_total'
        ]);

        $dados = [
            'user_id' => $this->usuario->id,
            'data' => $dataFechada->format('Y-m-d'),
            'horario' => '14:00',
            'quantidade' => 1,
            'grupo' => false
        ];

        $this->expectException(\Exception::class);
        $this->servico->executar($dados);
    }

    public function test_nao_criar_agendamento_sem_vagas()
    {
        $dataAgendamento = Carbon::tomorrow()->addDays(3);
        if ($dataAgendamento->isMonday()) {
            $dataAgendamento->addDay();
        }
        
        // Cria agendamentos que esgotem as vagas
        Agendamento::factory()->count(10)->create([
            'data' => $dataAgendamento->format('Y-m-d'),
            'horario' => '14:00:00',
            'quantidade' => 5 // Total: 50 vagas
        ]);

        $dados = [
            'user_id' => $this->usuario->id,
            'data' => $dataAgendamento->format('Y-m-d'),
            'horario' => '14:00',
            'quantidade' => 1,
            'grupo' => false
        ];

        $this->expectException(ConflitoDeHorarioException::class);
        $this->servico->executar($dados);
    }

    public function test_criar_agendamento_individual()
    {
        $dados = [
            'user_id' => $this->usuario->id,
            'data' => Carbon::tomorrow()->addDays(1)->format('Y-m-d'),
            'horario' => '10:00',
            'quantidade' => 1,
            'grupo' => false
        ];

        $resultado = $this->servico->executar($dados);

        $this->assertDatabaseHas('agendamentos', [
            'user_id' => $this->usuario->id,
            'quantidade' => 1,
            'grupo' => false
        ]);
    }

    public function test_criar_agendamento_em_grupo()
    {
        $dados = [
            'user_id' => $this->usuario->id,
            'data' => Carbon::tomorrow()->addDays(1)->format('Y-m-d'),
            'horario' => '15:00',
            'quantidade' => 4,
            'grupo' => true,
            'observacao' => 'Agendamento em grupo'
        ];

        $resultado = $this->servico->executar($dados);

        $this->assertDatabaseHas('agendamentos', [
            'user_id' => $this->usuario->id,
            'quantidade' => 4,
            'grupo' => true,
            'observacao' => 'Agendamento em grupo'
        ]);
    }
}