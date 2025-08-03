<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domains\Usuario\Entities\Usuario;
use App\Domains\Usuario\Repositories\ContratoUsuarioRepositorio;
use App\Models\User;

class EloquentUsuarioRepositorio implements ContratoUsuarioRepositorio
{
    public function buscarPorEmail(string $email): ?Usuario
    {
        $user = User::where('email', $email)->first();
        if (! $user) {
            return null;
        }

        return new Usuario($user->id, $user->name, $user->email, $user->password);
    }

    public function salvar(Usuario $usuario): void
    {
        $user = User::find($usuario->id) ?? new User;
        $user->id = $usuario->id;
        $user->name = $usuario->nome;
        $user->email = $usuario->email;
        $user->password = $usuario->senha;
        $user->save();
    }
}
