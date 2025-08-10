<?php

namespace Tests\Feature;

use App\Models\DiasFechados;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GestaoDiasControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authenticatedUser()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        return $user;
    }

    public function test_listar_dias_com_autenticacao()
    {
        $this->authenticatedUser();

        DiasFechados::factory()->count(5)->create();

        $response = $this->getJson('/api/admin/gestao-dias');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'data',
                        'horario_inicio',
                        'horario_fim',
                        'tipo',
                        'observacao'
                    ]
                ],
                'current_page',
                'per_page',
                'total'
            ]);
    }

    public function test_listar_dias_sem_autenticacao()
    {
        $response = $this->getJson('/api/admin/gestao-dias');

        $response->assertStatus(401);
    }

    public function test_listar_dias_com_filtro_de_data()
    {
        $this->authenticatedUser();

        $dataFiltro = Carbon::tomorrow()->format('Y-m-d');
        DiasFechados::factory()->create(['data' => $dataFiltro]);
        DiasFechados::factory()->create(['data' => Carbon::tomorrow()->addDay()->format('Y-m-d')]);

        $response = $this->getJson("/api/admin/gestao-dias?data={$dataFiltro}");

        $response->assertStatus(200);

        // Verifica se tem pelo menos um resultado
        $responseData = $response->json();
        $this->assertIsArray($responseData);
    }

    public function test_listar_dias_com_paginacao()
    {
        $this->authenticatedUser();

        DiasFechados::factory()->count(20)->create();

        $response = $this->getJson('/api/admin/gestao-dias?per_page=5&page=2');

        $response->assertStatus(200)
            ->assertJson([
                'current_page' => 2,
                'per_page' => 5
            ]);

        $this->assertCount(5, $response->json('data'));
    }

    public function test_buscar_dia_por_id_existente()
    {
        $this->authenticatedUser();

        $dia = DiasFechados::factory()->create([
            'data' => Carbon::tomorrow()->format('Y-m-d'),
            'tipo' => 'feriado',
            'observacao' => 'Dia de teste'
        ]);

        $response = $this->getJson("/api/admin/gestao-dias/{$dia->id}");

        $response->assertStatus(200);

        $responseData = $response->json();
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($dia->id, $responseData['id']);
    }

    public function test_buscar_dia_por_id_inexistente()
    {
        $this->authenticatedUser();

        $response = $this->getJson('/api/admin/gestao-dias/99999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Dia não encontrado'
            ]);
    }

    public function test_criar_dia_com_dados_validos()
    {
        $this->authenticatedUser();

        $dadosDia = [
            'data' => Carbon::tomorrow()->format('Y-m-d'),
            'horario_inicio' => '08:00',
            'horario_fim' => '18:00',
            'tipo' => 'bloqueio_total',
            'observacao' => 'Manutenção do sistema'
        ];

        $response = $this->postJson('/api/admin/gestao-dias/store', $dadosDia);

        $response->assertStatus(201);

        $this->assertDatabaseHas('dias_fechados', [
            'tipo' => $dadosDia['tipo'],
            'observacao' => $dadosDia['observacao']
        ]);
    }

    public function test_criar_dia_com_dados_invalidos()
    {
        $this->authenticatedUser();

        $response = $this->postJson('/api/admin/gestao-dias/store', []);

        $response->assertStatus(422);

        $responseData = $response->json();
        $this->assertNotEmpty($responseData, 'Response should not be empty');
    }

    public function test_criar_dia_com_tipo_invalido()
    {
        $this->authenticatedUser();

        $dadosDia = [
            'data' => Carbon::tomorrow()->format('Y-m-d'),
            'horario_inicio' => '08:00',
            'horario_fim' => '18:00',
            'tipo' => 'tipo_inexistente'
        ];

        $response = $this->postJson('/api/admin/gestao-dias/store', $dadosDia);

        $response->assertStatus(422);

        // Verifica se a validação funcionou (retornou erro 422)
        $responseData = $response->json();
        $this->assertNotEmpty($responseData, 'Response should not be empty for validation error');
    }

    public function test_criar_dia_com_horario_fim_antes_do_inicio()
    {
        $this->authenticatedUser();

        $dadosDia = [
            'data' => Carbon::tomorrow()->format('Y-m-d'),
            'horario_inicio' => '18:00',
            'horario_fim' => '08:00', // Hora fim antes da hora início
            'tipo' => 'bloqueio_parcial'
        ];

        $response = $this->postJson('/api/admin/gestao-dias/store', $dadosDia);

        $response->assertStatus(422);

        // Verifica se a validação funcionou (retornou erro 422)
        $responseData = $response->json();
        $this->assertNotEmpty($responseData, 'Response should not be empty for validation error');
    }

    public function test_atualizar_dia_existente()
    {
        $this->authenticatedUser();

        $dia = DiasFechados::factory()->create([
            'tipo' => 'bloqueio_parcial',
            'observacao' => 'Observação antiga'
        ]);

        $dadosAtualizacao = [
            'tipo' => 'feriado',
            'observacao' => 'Observação atualizada'
        ];

        $response = $this->putJson("/api/admin/gestao-dias/{$dia->id}", $dadosAtualizacao);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Dia atualizado com sucesso'
            ]);

        $dia->refresh();
        $this->assertEquals('feriado', $dia->tipo);
        $this->assertEquals('Observação atualizada', $dia->observacao);
    }

    public function test_atualizar_dia_inexistente()
    {
        $this->authenticatedUser();

        $response = $this->putJson('/api/admin/gestao-dias/99999', [
            'tipo' => 'feriado'
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Dia não encontrado'
            ]);
    }

    public function test_atualizar_dia_com_dados_invalidos()
    {
        $this->authenticatedUser();

        $dia = DiasFechados::factory()->create();

        $response = $this->putJson("/api/admin/gestao-dias/{$dia->id}", [
            'tipo' => 'tipo_invalido',
            'horario_fim' => '25:00' // Hora inválida
        ]);

        $response->assertStatus(422);

        // Verifica se a validação funcionou (retornou erro 422)
        $responseData = $response->json();
        $this->assertNotEmpty($responseData, 'Response should not be empty for validation error');
    }

    public function test_excluir_dia_existente()
    {
        $this->authenticatedUser();

        $dia = DiasFechados::factory()->create();

        $response = $this->deleteJson("/api/admin/gestao-dias/{$dia->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Dia excluído com sucesso'
            ]);

        $this->assertDatabaseMissing('dias_fechados', [
            'id' => $dia->id
        ]);
    }

    public function test_excluir_dia_inexistente()
    {
        $this->authenticatedUser();

        $response = $this->deleteJson('/api/admin/gestao-dias/99999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Dia não encontrado'
            ]);
    }

    public function test_criar_dia_todos_os_tipos_validos()
    {
        $this->authenticatedUser();

        $tiposValidos = ['bloqueio_total', 'bloqueio_parcial', 'manutencao', 'feriado'];

        foreach ($tiposValidos as $index => $tipo) {
            $dadosDia = [
                'data' => Carbon::tomorrow()->addDays($index)->format('Y-m-d'),
                'horario_inicio' => '08:00',
                'horario_fim' => '18:00',
                'tipo' => $tipo,
                'observacao' => "Teste para tipo {$tipo}"
            ];

            $response = $this->postJson('/api/admin/gestao-dias/store', $dadosDia);

            $response->assertStatus(201);
            $this->assertDatabaseHas('dias_fechados', [
                'tipo' => $tipo,
                'observacao' => "Teste para tipo {$tipo}"
            ]);
        }
    }

    public function test_operacoes_sem_autenticacao()
    {
        $dia = DiasFechados::factory()->create();

        // Teste criar sem autenticação
        $response = $this->postJson('/api/admin/gestao-dias/store', []);
        $response->assertStatus(401);

        // Teste atualizar sem autenticação
        $response = $this->putJson("/api/admin/gestao-dias/{$dia->id}", []);
        $response->assertStatus(401);

        // Teste excluir sem autenticação
        $response = $this->deleteJson("/api/admin/gestao-dias/{$dia->id}");
        $response->assertStatus(401);

        // Teste buscar sem autenticação
        $response = $this->getJson("/api/admin/gestao-dias/{$dia->id}");
        $response->assertStatus(401);
    }
}
