<?php

namespace App\Application\Usuarios\Servicos;

use App\Domains\Usuario\Exceptions\UsuarioNaoEncontradoException;
use App\Domains\Usuario\Repositories\ContratoUsuarioRepositorio;
use App\Mail\ResetSenhaMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ResetarSenhaUsuarioServico
{
    public function __construct(
        protected ContratoUsuarioRepositorio $repositorio
    ) {}

    public function solicitarResetSenha(string $email): void
    {
        $usuario = $this->repositorio->buscarPorEmail($email);
//        if (! $usuario) {
//            throw new UsuarioNaoEncontradoException('Usuário não encontrado');
//        }
        $token = Str::random(60);
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );
        Mail::to($email)->send(new ResetSenhaMail($email, $token));
    }

    public function validarToken(string $email, string $token): bool
    {
        $reset = DB::table('password_resets')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        return (bool) $reset;
    }

    public function redefinirSenha(string $email, string $token, string $novaSenha): void
    {
        if (! $this->validarToken($email, $token)) {
            throw new \Exception('Token inválido ou expirado');
        }
        $usuario = $this->repositorio->buscarPorEmail($email);
        if (! $usuario) {
            throw new UsuarioNaoEncontradoException('Usuário não encontrado');
        }
        $usuario->senha = Hash::make($novaSenha);
        $this->repositorio->salvar($usuario);
        DB::table('password_resets')->where('email', $email)->delete();
    }
}
