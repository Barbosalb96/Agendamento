<?php

use App\Http\Middleware\ForceJsonMiddleware;
use App\Jobs\MailDispatchDefault;
use App\Models\ExceptionLog;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api([
            ForceJsonMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            return response()->json([
                'mensagem' => 'Registro não encontrado.',
                'erro' => 'O recurso solicitado não existe no sistema.',
                'codigo' => 404
            ], 404);
        });

        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'mensagem' => 'Página ou endpoint não encontrado.',
                'erro' => 'A URL solicitada não existe neste servidor.',
                'codigo' => 404
            ], 404);
        });

        $exceptions->renderable(function (AuthenticationException $e, $request) {
            return response()->json([
                'mensagem' => 'Acesso não autorizado.',
                'erro' => 'Você precisa estar autenticado para acessar este recurso.',
                'codigo' => 401
            ], 401);
        });

        $exceptions->renderable(function (AuthorizationException $e, $request) {
            return response()->json([
                'mensagem' => 'Acesso negado.',
                'erro' => 'Você não possui permissão para realizar esta ação.',
                'codigo' => 403
            ], 403);
        });

        $exceptions->renderable(function (ValidationException $e, $request) {
            return response()->json([
                'mensagem' => 'Dados inválidos fornecidos.',
                'erro' => 'Verifique os campos obrigatórios e tente novamente.',
                'erros_validacao' => $e->errors(),
                'codigo' => 422
            ], 422);
        });

        $exceptions->renderable(function (Throwable $e, $request) {
            if ($e instanceof HttpException) {
                return null;
            }
            $errorData = [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];

            ExceptionLog::create($errorData);
            
            // Enviar email de notificação de erro
            dispatch(new MailDispatchDefault(
                'Error aplicação Agendamento - Governo do Maranhão',
                [
                    'error' => $errorData,
                    'timestamp' => now()->format('d/m/Y H:i:s'),
                    'server' => request()->getHost(),
                ],
                'error-report',
                'barbosalucaslbs96@gmail.com',
            ));

            return response()->json([
                'mensagem' => 'Erro interno do servidor.',
                'erro' => 'Ocorreu um erro inesperado. Nossa equipe técnica foi notificada.',
                'codigo' => 500,
                'contato' => 'Se o problema persistir, entre em contato com o suporte técnico.'
            ], 500);
        });
    })->create();
