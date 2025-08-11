<?php

namespace Tests\Unit;

use App\Domains\Agendamento\Entities\Agendamento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendamentoModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_agendamento_pode_ser_criado()
    {
        $user = User::factory()->create();

        $agendamento = Agendamento::factory()->create([
            'user_id' => $user->id,
            'quantidade' => 3,
            'grupo' => true,
        ]);

        $this->assertDatabaseHas('agendamentos', [
            'id' => $agendamento->id,
            'user_id' => $user->id,
            'quantidade' => 3,
            'grupo' => true,
        ]);
    }

    public function test_agendamento_tem_relacao_com_usuario()
    {
        $agendamento = Agendamento::factory()->create();

        $this->assertInstanceOf(User::class, $agendamento->user);
    }

    public function test_agendamento_tem_uuid()
    {
        $agendamento = Agendamento::factory()->create();

        $this->assertNotNull($agendamento->uuid);
        $this->assertIsString($agendamento->uuid);
    }

    public function test_agendamento_data_formatada()
    {
        $agendamento = Agendamento::factory()->create([
            'data' => '2025-12-15',
        ]);

        $this->assertEquals('15/12/2025', $agendamento->data_formatada);
    }

    public function test_agendamento_horario_formatado()
    {
        $agendamento = Agendamento::factory()->create([
            'horario' => '14:30',
        ]);

        $this->assertEquals('14:30', $agendamento->horario_formatado);
    }

    public function test_usuario_tem_agendamentos()
    {
        $user = User::factory()->create();

        Agendamento::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $this->assertCount(3, $user->agendamentos);
    }
}
