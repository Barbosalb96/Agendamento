<?php

namespace Database\Factories;

use App\Models\DiasFechados;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DiasFechadosFactory extends Factory
{
    protected $model = DiasFechados::class;

    public function definition(): array
    {
        return [
            'data' => Carbon::today()->addDays(rand(1, 60)),
            'horario_inicio' => '08:00',
            'horario_fim' => '18:00',
            'tipo' => fake()->randomElement(['bloqueio_total', 'bloqueio_parcial', 'manutencao', 'feriado']),
            'observacao' => fake()->optional()->sentence(),
        ];
    }

    public function bloqueioTotal(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'bloqueio_total',
            'horario_inicio' => '00:00',
            'horario_fim' => '23:59',
        ]);
    }

    public function bloqueioParcial(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'bloqueio_parcial',
        ]);
    }

    public function feriado(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'feriado',
            'observacao' => 'Feriado nacional',
        ]);
    }
}
