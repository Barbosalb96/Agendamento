<?php

namespace App\Http\Controllers;

use App\Application\Usuarios\Services\LoginUsuarioServico;
use App\Application\Usuarios\Services\ResetarSenhaUsuarioServico;
use App\Domains\Agendamento\Entities\Agendamento;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RequestResetSenhaRequest;
use App\Http\Requests\ResetSenhaRequest;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUsuarioRepositorio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class UsuarioControlador extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Autenticação"},
     *     summary="Login do usuário",
     *     description="Autentica um usuário e retorna um token de acesso",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="token", type="string", example="1|abc123..."),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nome", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", example="user@example.com")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="mensagem", type="string", example="Usuário ou senha inválidos")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/password/reset",
     *     tags={"Autenticação"},
     *     summary="Redefinir senha",
     *     description="Redefine a senha do usuário usando token de reset",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","token","nova_senha"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="token", type="string", example="abc123..."),
     *             @OA\Property(property="nova_senha", type="string", example="newpassword123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Senha redefinida com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="mensagem", type="string", example="Senha redefinida com sucesso")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Erro na redefinição",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="mensagem", type="string", example="Token inválido")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/password/request-reset",
     *     tags={"Autenticação"},
     *     summary="Solicitar reset de senha",
     *     description="Solicita um token para reset de senha por email",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Token enviado",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="mensagem", type="string", example="Token enviado para o e-mail se existir um usuário com esse e-mail.")
     *         )
     *     )
     * )
     */
    public function requestReset(RequestResetSenhaRequest $request)
    {
        $servico = new ResetarSenhaUsuarioServico(new EloquentUsuarioRepositorio);

        $servico->solicitarResetSenha($request->email);

        return response()->json(['mensagem' => 'Token enviado para o e-mail se existir um usuário com esse e-mail.']);
    }

    /**
     * @OA\Get(
     *     path="/api/validar-qrcode/{uuid}",
     *     tags={"QR Code"},
     *     summary="Validar QR Code",
     *     description="Valida um QR Code de agendamento",
     *
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string", example="abc123-def456-ghi789")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="QR Code válido",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="mensagem", type="string", example="QR Code válido"),
     *             @OA\Property(property="agendamento", type="object",
     *                 @OA\Property(property="data", type="string", example="15/12/2023"),
     *                 @OA\Property(property="horario", type="string", example="14:00"),
     *                 @OA\Property(property="grupo", type="string", example="Sim"),
     *                 @OA\Property(property="quantidade", type="integer", example=2),
     *                 @OA\Property(property="observacao", type="string", example="Observação do agendamento")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="QR Code inválido",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="mensagem", type="string", example="QR Code inválido ou agendamento não encontrado")
     *         )
     *     )
     * )
     */
    public function validarQRCode(string $uuid): JsonResponse
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
