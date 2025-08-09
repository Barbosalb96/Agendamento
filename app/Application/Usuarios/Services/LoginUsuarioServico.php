<?php

namespace App\Application\Usuarios\Services;

use App\Domains\Usuario\Repositories\ContratoUsuarioRepositorio;

class LoginUsuarioServico
{
    public function __construct(
        protected ContratoUsuarioRepositorio $repositorio
    ) {}

    public function executar(string $email, string $senha)
    {
        $usuario = $this->repositorio->buscarPorEmail($email);
        if (! $usuario || ! \Illuminate\Support\Facades\Hash::check($senha, $usuario->senha)) {
            throw new \App\Domains\Usuario\Exceptions\UsuarioNaoEncontradoException('Usuário ou senha inválidos');
        }

        return $usuario;
    }
}
