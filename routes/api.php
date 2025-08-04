<?php

use App\Http\Controllers\AgendamentoControlador;
use App\Http\Controllers\UsuarioControlador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('agendamento')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AgendamentoControlador::class, 'index']);
    Route::post('/', [AgendamentoControlador::class, 'agendar']);
});

Route::post('/login', [UsuarioControlador::class, 'login']);
Route::post('/password/reset', [UsuarioControlador::class, 'resetSenha']);
Route::post('/password/request-reset', [UsuarioControlador::class, 'requestReset']);

Route::get('validar-qrcode/{uuid}', [UsuarioControlador::class, 'validarQRCode'])
    ->name('validar-qrcode');