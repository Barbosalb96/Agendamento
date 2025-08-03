<?php

namespace App\Domains\Usuario\Repositories;

use App\Domains\Usuario\Entities\Usuario;

interface ContratoUsuarioRepositorio
{
    public function buscarPorEmail(string $email): ?Usuario;

    public function salvar(Usuario $usuario): void;
}
