<?php

namespace App\Providers;

use App\Domains\Agendamento\Repositories\ContratoAgendamentoRepositorio;
use App\Domains\GestaoDias\Repositories\GestaoDiasRepositorio as ContratoGestaoDiasRepositorio;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentAgendamentoRepositorio;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentGestaoDias;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(ContratoAgendamentoRepositorio::class, EloquentAgendamentoRepositorio::class);
        $this->app->bind(ContratoGestaoDiasRepositorio::class, EloquentGestaoDias::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
