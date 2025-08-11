<?php

namespace Database\Factories;

use App\Domains\Agendamento\Entities\Agendamento;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AgendamentoFactory extends Factory
{
    protected $model = Agendamento::class;

    public function definition(): array
    {
        $grupo = fake()->boolean(30);
        // Use valid CPFs that pass validation
        $validCpfs = ['26085427397', '11346402091', '24016697772', '05921809159', '35279336353', '80216860507', '16020379759', '34116402379', '52581667133', '05021517507'];
        $cpf = fake()->randomElement($validCpfs);

        return [
            'uuid' => fake()->uuid(),
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => $cpf,
            'rg' => fake()->numerify('#########'),
            'telefone' => fake()->unique()->numerify('11#########'),
            'nacionalidade' => fake()->randomElement(['brasileiro', 'estrangeiro']),
            'nacionalidade_grupo' => fake()->randomElement(['brasileiro', 'estrangeiro']),
            'deficiencia' => fake()->boolean(10),
            'data' => Carbon::today()->addDays(rand(2, 30))->format('Y-m-d'),
            'horario' => sprintf('%02d:00', rand(9, 17)),
            'grupo' => $grupo,
            'quantidade' => $grupo ? rand(10, 50) : 1,
            'observacao' => fake()->optional()->sentence(),
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
            'quantidade' => rand(10, 50),
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
