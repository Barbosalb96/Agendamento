<?php

namespace App\Providers;

use App\Domains\Agendamento\Repositories\ContratoAgendamentoRepositorio;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentAgendamentoRepositorio;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(ContratoAgendamentoRepositorio::class, EloquentAgendamentoRepositorio::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
