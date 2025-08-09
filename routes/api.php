<?php

use App\Http\Controllers\AgendamentoControlador;
use App\Http\Controllers\GestaoDiasController;
use App\Http\Controllers\UsuarioControlador;
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
        Route::get('/', [AgendamentoControlador::class, 'index']);
        Route::post('/', [AgendamentoControlador::class, 'agendar']);
//        Route::get('/{id}', [AgendamentoControlador::class, 'show']);
        Route::delete('/{id}', [AgendamentoControlador::class, 'destroy']);
        Route::get('/vagas-por-horario', [AgendamentoControlador::class, 'vagasPorHorario']);
    });
});

Route::post('/login', [UsuarioControlador::class, 'login']);
Route::post('/password/reset', [UsuarioControlador::class, 'resetSenha']);
Route::post('/password/request-reset', [UsuarioControlador::class, 'requestReset']);

Route::get('validar-qrcode/{uuid}', [UsuarioControlador::class, 'validarQRCode'])
    ->name('validar-qrcode');
