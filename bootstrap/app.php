<?php

use App\Http\Middleware\ForceJsonMiddleware;
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
                'message' => 'Registro não encontrado.',
            ], 404);
        });

        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'message' => 'Endpoint ou página não encontrada.',
            ], 404);
        });

        $exceptions->renderable(function (AuthenticationException $e, $request) {
            return response()->json([
                'message' => 'Você precisa estar autenticado para acessar este recurso.',
            ], 401);
        });

        $exceptions->renderable(function (AuthorizationException $e, $request) {
            return response()->json([
                'message' => 'Você não tem permissão para acessar este recurso.',
            ], 403);
        });

        $exceptions->renderable(function (ValidationException $e, $request) {
            return response()->json([
                'message' => 'Os dados fornecidos são inválidos.',
                'errors' => $e->errors(),
            ], 422);
        });

        $exceptions->renderable(function (Throwable $e, $request) {
            if ($e instanceof HttpException) {
                return null;
            }

            return response()->json([
                'message' => 'Ocorreu um erro inesperado no servidor. Tente novamente mais tarde.',
            ], 500);
        });
    })->create();
