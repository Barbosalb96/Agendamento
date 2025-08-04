<?php

namespace App\Http\Requests;

use App\Domains\Agendamento\Entities\Agendamento;
use App\Models\DiasFechados;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StoreAgendamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuid' => ['nullable', 'uuid'],
            'user_id' => ['required', 'exists:users,id'],
            'data' => ['required', 'date'],
            'horario' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $time = \Carbon\Carbon::createFromFormat('H:i', $value);
                    if ($time->minute !== 0) {
                        $fail('Só é permitido agendar em horários cheios (ex: 08:00, 09:00, 10:00).');
                    }
                },
            ],
            'grupo' => ['nullable', 'boolean'],
            'observacao' => ['nullable', 'string'],
            'quantidade' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->input('data');
            $horario = $this->input('horario');
            $userId = $this->input('user_id');
            $grupo = $this->input('grupo');
            $quantidade = (int) $this->input('quantidade');

            if (! $data || ! $horario || ! $quantidade || ! $userId) {
                return;
            }

            if ($grupo == false && $quantidade > 1) {
                $validator->errors()->add('data', 'somente grupos podem ter mais de uma pessoa na quantidade.');
            }

            $dataAgendada = Carbon::parse($data)->startOfDay();
            $amanha = now()->addDay()->startOfDay();

            if ($dataAgendada->lt($amanha)) {
                $validator->errors()->add('data', 'A data do agendamento deve ser a partir de amanhã.');
            }

            $diaFechado = DiasFechados::whereDate('data', $dataAgendada)->exists();
            if ($diaFechado) {
                $validator->errors()->add('data', 'Não é possível agendar nesta data, pois está marcada como fechada.');
            }

            $totalAgendado = Agendamento::whereDate('data', $data)
                ->where('horario', $horario)
                ->sum('quantidade');

            if (($totalAgendado + $quantidade) > 50) {
                $disponiveis = max(0, 50 - $totalAgendado);
                $validator->errors()->add(
                    'quantidade',
                    "Já existem $totalAgendado pessoas agendadas para este horário. Restam apenas $disponiveis vagas."
                );
            }

            // ❗ Verificar se o mesmo usuário já tem agendamento dentro de 1 hora no mesmo dia
            $horaAgendada = Carbon::createFromFormat('H:i', $horario);
            $inicioJanela = $horaAgendada->copy()->subMinutes(59)->format('H:i:s');
            $fimJanela = $horaAgendada->copy()->addMinutes(59)->format('H:i:s');

            $jaTemOutro = Agendamento::whereDate('data', $data)
                ->where('user_id', $userId)
                ->whereBetween('horario', [$inicioJanela, $fimJanela])
                ->exists();

            if ($jaTemOutro) {
                $validator->errors()->add(
                    'horario',
                    'Você já possui um agendamento em um horário muito próximo. O intervalo mínimo entre agendamentos é de 1 hora.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'O campo usuário é obrigatório.',
            'user_id.exists' => 'O usuário informado não existe.',
            'data.required' => 'A data do agendamento é obrigatória.',
            'data.date' => 'A data informada é inválida.',
            'horario.required' => 'O horário do agendamento é obrigatório.',
            'horario.date_format' => 'O horário deve estar no formato HH:mm.',
            'quantidade.required' => 'Informe a quantidade de pessoas.',
            'quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade mínima é 1.',
            'quantidade.max' => 'A quantidade máxima por agendamento é de 50 pessoas.',
        ];
    }
}
