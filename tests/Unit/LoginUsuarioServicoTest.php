<?php

namespace Tests\Unit;

use App\Application\Usuarios\Services\LoginUsuarioServico;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUsuarioRepositorio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginUsuarioServicoTest extends TestCase
{
    use RefreshDatabase;

    private LoginUsuarioServico $servico;

    protected function setUp(): void
    {
        parent::setUp();

        $this->servico = new LoginUsuarioServico(
            new EloquentUsuarioRepositorio()
        );
    }

    public function test_login_com_credenciais_validas()
    {
        $user = User::factory()->create([
            'name' => 'João Teste',
            'email' => 'joao@teste.com',
            'password' => Hash::make('senha123')
        ]);

        $resultado = $this->servico->executar('joao@teste.com', 'senha123');

        $this->assertNotNull($resultado);
        $this->assertEquals($user->id, $resultado->id);
        $this->assertEquals('João Teste', $resultado->nome);
        $this->assertEquals('joao@teste.com', $resultado->email);
    }

    public function test_login_com_senha_incorreta()
    {
        $user = User::factory()->create([
            'email' => 'joao@teste.com',
            'password' => Hash::make('senha123')
        ]);

        $this->expectException(\Exception::class);
        $this->servico->executar('joao@teste.com', 'senhaerrada');
    }

    public function test_login_com_email_inexistente()
    {
        $this->expectException(\Exception::class);
        $this->servico->executar('inexistente@teste.com', 'qualquersenha');
    }

    public function test_login_com_email_vazio()
    {
        $this->expectException(\Exception::class);
        $this->servico->executar('', 'senha123');
    }

    public function test_login_com_senha_vazia()
    {
        $user = User::factory()->create([
            'email' => 'joao@teste.com',
            'password' => Hash::make('senha123')
        ]);

        $this->expectException(\Exception::class);
        $this->servico->executar('joao@teste.com', '');
    }

    public function test_login_case_sensitive_senha()
    {
        $user = User::factory()->create([
            'email' => 'joao@teste.com',
            'password' => Hash::make('Senha123')
        ]);

        // Deve falhar com senha em case diferente
        $this->expectException(\Exception::class);
        $this->servico->executar('joao@teste.com', 'senha123');
    }

    public function test_login_retorna_dados_usuario()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('senha123')
        ]);

        $resultado = $this->servico->executar('test@example.com', 'senha123');
        
        $this->assertNotNull($resultado);
        $this->assertEquals($user->id, $resultado->id);
        $this->assertEquals('Test User', $resultado->nome);
        $this->assertEquals('test@example.com', $resultado->email);
    }
}
