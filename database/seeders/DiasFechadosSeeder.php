<?php

namespace Database\Seeders;

use App\Models\DiasFechados;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DiasFechadosSeeder extends Seeder
{
    public function run(): void
    {
        $ano = now()->year;

        $feriados = [
            // 🟥 FERIADOS NACIONAIS (fixos)
            ['data' => "$ano-01-01", 'tipo' => 'nacional', 'observacao' => 'Confraternização Universal'],
            ['data' => "$ano-04-21", 'tipo' => 'nacional', 'observacao' => 'Tiradentes'],
            ['data' => "$ano-05-01", 'tipo' => 'nacional', 'observacao' => 'Dia do Trabalho'],
            ['data' => "$ano-09-07", 'tipo' => 'nacional', 'observacao' => 'Independência do Brasil'],
            ['data' => "$ano-10-12", 'tipo' => 'nacional', 'observacao' => 'Nossa Senhora Aparecida'],
            ['data' => "$ano-11-02", 'tipo' => 'nacional', 'observacao' => 'Finados'],
            ['data' => "$ano-11-15", 'tipo' => 'nacional', 'observacao' => 'Proclamação da República'],
            ['data' => "$ano-12-25", 'tipo' => 'nacional', 'observacao' => 'Natal'],

            // 🟦 FERIADOS ESTADUAIS (Maranhão)
            ['data' => "$ano-07-28", 'tipo' => 'estadual', 'observacao' => 'Adesão do Maranhão à Independência'],
        ];

        // 🟨 FERIADOS MÓVEIS (baseados na Páscoa)
        $pascoa = Carbon::createFromTimestamp(easter_date($ano));
        $feriadosMoveis = [
            ['data' => $pascoa->copy()->subDays(47)->toDateString(), 'tipo' => 'nacional', 'observacao' => 'Carnaval'],
            ['data' => $pascoa->copy()->subDays(2)->toDateString(),  'tipo' => 'nacional', 'observacao' => 'Sexta-feira Santa'],
            ['data' => $pascoa->toDateString(),                      'tipo' => 'nacional', 'observacao' => 'Páscoa'],
            ['data' => $pascoa->copy()->addDays(60)->toDateString(), 'tipo' => 'nacional', 'observacao' => 'Corpus Christi'],
        ];

        foreach (array_merge($feriados, $feriadosMoveis) as $feriado) {
            DiasFechados::firstOrCreate(
                ['data' => $feriado['data'], 'observacao' => $feriado['observacao']],
                [
                    'tipo' => $feriado['tipo'],
                    'horario_inicio' => null,
                    'horario_fim' => null,
                ]
            );
        }
    }
}
