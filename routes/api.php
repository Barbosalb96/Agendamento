<?php

use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\GestaoDiasController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::prefix('gestao-dias')->group(function () {
        Route::get('/', [GestaoDiasController::class, 'index']);
        Route::get('/{id}', [GestaoDiasController::class, 'show']);
        Route::post('/store', [GestaoDiasController::class, 'store']);
        Route::put('/{id}', [GestaoDiasController::class, 'update']);
        Route::delete('/{id}', [GestaoDiasController::class, 'destroy']);
    });

    Route::prefix('agendamento')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [AgendamentoController::class, 'index']);
      Route::get('/total-dia', [AgendamentoController::class, 'agendamentoDia']);
        Route::get('/find/{uuid}', [AgendamentoController::class, 'show']);
        Route::delete('/{id}', [AgendamentoController::class, 'destroy']);
        Route::get('relatorio',[AgendamentoController::class,'relatorio']);
        Route::post('confirmar',[AgendamentoController::class,'confirmar']);
    });
});

Route::post('/login', [UsuarioController::class, 'login']);
Route::post('/password/reset', [UsuarioController::class, 'resetSenha']);
Route::post('/password/request-reset', [UsuarioController::class, 'requestReset']);

Route::get('validar-qrcode/{uuid}', [AgendamentoController::class, 'validarQRCode'])
    ->name('validar-qrcode');

Route::post('/agendar', [AgendamentoController::class, 'agendar']);
Route::get('/vagas-por-horario', [AgendamentoController::class, 'vagasPorHorario']);

