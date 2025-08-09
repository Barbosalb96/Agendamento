<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Log do acesso para monitoramento de segurança
    \Log::info('Acesso à página inicial do sistema governamental', [
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'timestamp' => now(),
        'url' => request()->fullUrl()
    ]);

    return view('governo');
});
