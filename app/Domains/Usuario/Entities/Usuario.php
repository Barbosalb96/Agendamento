<?php

namespace App\Domains\Usuario\Entities;

class Usuario
{
    public function __construct(
        public readonly int $id,
        public string $nome,
        public string $email,
        public string $senha
    ) {}
}
