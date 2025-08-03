<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UsuarioAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_sucesso()
    {
        $user = User::factory()->create([
            'email' => 'teste@exemplo.com',
            'password' => Hash::make('senha123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'teste@exemplo.com',
            'password' => 'senha123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user' => ['id', 'nome', 'email']]);
    }

    public function test_login_falha_senha_incorreta()
    {
        $user = User::factory()->create([
            'email' => 'teste2@exemplo.com',
            'password' => Hash::make('senha123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'teste2@exemplo.com',
            'password' => 'errada',
        ]);

        $response->assertStatus(401)
            ->assertJson(['mensagem' => 'Usuário ou senha inválidos']);
    }

    public function test_reset_senha_sucesso()
    {
        $user = User::factory()->create([
            'email' => 'reset@exemplo.com',
            'password' => Hash::make('antiga123'),
        ]);

        // Solicita reset para gerar token
        $this->postJson('/api/password/request-reset', [
            'email' => 'reset@exemplo.com',
        ]);
        $token = \DB::table('password_resets')->where('email', 'reset@exemplo.com')->value('token');

        $response = $this->postJson('/api/password/reset', [
            'email' => 'reset@exemplo.com',
            'token' => $token,
            'nova_senha' => 'novaSenha456',
        ]);

        $response->assertStatus(200)
            ->assertJson(['mensagem' => 'Senha redefinida com sucesso']);

        $user->refresh();
        $this->assertTrue(Hash::check('novaSenha456', $user->fresh()->password));
    }

    public function test_reset_senha_usuario_inexistente()
    {
        // Solicita reset para e-mail inexistente (não deve falhar, mas não gera token)
        $this->postJson('/api/password/request-reset', [
            'email' => 'naoexiste@exemplo.com',
        ]);
        $token = 'tokeninvalido';
        $response = $this->postJson('/api/password/reset', [
            'email' => 'naoexiste@exemplo.com',
            'token' => $token,
            'nova_senha' => 'qualquer',
        ]);
        $response->assertStatus(400)
            ->assertJson(['mensagem' => 'Token inválido ou expirado']);
    }
}
