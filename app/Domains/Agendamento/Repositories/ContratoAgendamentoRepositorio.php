<?php

namespace App\Domains\Agendamento\Repositories;

interface ContratoAgendamentoRepositorio
{
    public function salvar(array $agendamento);
    public function cancelar(string $id): void;
    public function buscar(array $filter);
}
