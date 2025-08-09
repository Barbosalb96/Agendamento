<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_pode_ser_criado()
    {
        $user = User::factory()->create([
            'name' => 'João Test',
            'email' => 'joao@test.com'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'João Test',
            'email' => 'joao@test.com'
        ]);
    }

    public function test_user_tem_uuid_publico()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->id_public);
        $this->assertIsString($user->id_public);
    }

    public function test_user_senha_e_hashed()
    {
        $user = User::factory()->create([
            'password' => Hash::make('senha123')
        ]);

        $this->assertTrue(Hash::check('senha123', $user->password));
    }

    public function test_user_campos_obrigatorios()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->cpf);
        $this->assertNotNull($user->rg);
        $this->assertNotNull($user->eelefone);
    }

    public function test_user_perfil_padrao()
    {
        $user = User::factory()->create();

        $this->assertContains($user->perfil, ['admin', 'individual', 'grupo']);
    }
}