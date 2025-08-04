<?php

namespace App\Http\Controllers;

use App\Application\Usuarios\Servicos\LoginUsuarioServico;
use App\Application\Usuarios\Servicos\ResetarSenhaUsuarioServico;
use App\Domains\Agendamento\Entities\Agendamento;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RequestResetSenhaRequest;
use App\Http\Requests\ResetSenhaRequest;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUsuarioRepositorio;
use App\Models\User;
use Carbon\Carbon;

class UsuarioControlador extends Controller
{
    public function login(LoginRequest $request)
    {
        $servico = new LoginUsuarioServico(new EloquentUsuarioRepositorio);
        try {
            $usuario = $servico->executar($request->email, $request->password);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => 'Usuário ou senha inválidos'], 401);
        }
        $userModel = User::find($usuario->id);
        $token = $userModel->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => [
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'email' => $usuario->email,
        ]]);
    }

    public function resetSenha(ResetSenhaRequest $request)
    {
        $servico = new ResetarSenhaUsuarioServico(new EloquentUsuarioRepositorio);
        try {
            $servico->redefinirSenha($request->email, $request->token, $request->nova_senha);
        } catch (\Exception $e) {
            return response()->json(['mensagem' => $e->getMessage()], 400);
        }

        return response()->json(['mensagem' => 'Senha redefinida com sucesso']);
    }

    public function requestReset(RequestResetSenhaRequest $request)
    {
        $servico = new ResetarSenhaUsuarioServico(new EloquentUsuarioRepositorio);

        $servico->solicitarResetSenha($request->email);

        return response()->json(['mensagem' => 'Token enviado para o e-mail se existir um usuário com esse e-mail.']);
    }

    public function validarQRCode(string $uuid)
    {

        $agendamento = Agendamento::where('uuid', $uuid)->first();

        if (! $agendamento) {
            return response()->json(['mensagem' => 'QR Code inválido ou agendamento não encontrado']);
        }
        $invalid = $agendamento->data >= Carbon::now() && $agendamento->horario > Carbon::now()->format('H:i');

        if (! $invalid) {
            return response()->json(['mensagem' => 'QR Code inválido horario de agendamento superior a hora marcada']);
        }

        return response()->json([
            'mensagem' => 'QR Code válido',
            'agendamento' => [
                'data' => $agendamento->data_formatada ?? Carbon::parse($agendamento->data)->format('d/m/Y'),
                'horario' => $agendamento->horario_formatado ?? substr($agendamento->horario, 0, 5),
                'grupo' => $agendamento->grupo ? 'Sim' : 'Não',
                'quantidade' => $agendamento->quantidade,
                'observacao' => $agendamento->observacao,
            ],
        ]);
    }
}
