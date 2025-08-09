<?php

namespace Database\Factories;

use App\Domains\Agendamento\Entities\Agendamento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AgendamentoFactory extends Factory
{
    protected $model = Agendamento::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'data' => Carbon::today()->addDays(rand(1, 30)),
            'horario' => sprintf('%02d:00', rand(9, 17)),
            'grupo' => fake()->boolean(30),
            'observacao' => fake()->optional()->sentence(),
            'quantidade' => rand(1, 5),
        ];
    }

    public function confirmado(): static
    {
        return $this->state(fn (array $attributes) => [
            'observacao' => 'Agendamento confirmado',
        ]);
    }

    public function cancelado(): static
    {
        return $this->state(fn (array $attributes) => [
            'observacao' => 'Agendamento cancelado',
        ]);
    }

    public function grupo(): static
    {
        return $this->state(fn (array $attributes) => [
            'grupo' => true,
            'quantidade' => rand(2, 5),
        ]);
    }

    public function individual(): static
    {
        return $this->state(fn (array $attributes) => [
            'grupo' => false,
            'quantidade' => 1,
        ]);
    }
}