<?php

namespace App\Http\Requests;

use App\Domains\Agendamento\Entities\Agendamento;
use App\Models\DiasFechados;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

class StoreAgendamentoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'uuid' => ['nullable', 'uuid'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'data' => ['required', 'date'],
            'horario' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $time = Carbon::createFromFormat('H:i', $value);
                    if ($time->minute !== 0) {
                        $fail('Só é permitido agendar em horários cheios (ex: 09:00, 10:00, 11:00).');

                        return;
                    }

                    $hour = (int)$time->format('H');
                    if ($hour < 9 || $hour > 17) {
                        $fail('Os horários disponíveis são apenas entre 09:00 e 17:00, em intervalos de 1 hora.');

                        return;
                    }
                },
            ],
            'grupo' => ['nullable', 'boolean'],
            'observacao' => ['nullable', 'string'],
            'quantidade' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }

    protected function prepareForValidation()
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $this->merge([
                "user_id" => auth()->user()->id
            ]);
        }

    }

    public function withValidator($validator)
    {
        /** @var Validator $validator */
        $validator->after(function (Validator $validator) {
            $data = $this->input('data');
            $horario = $this->input('horario');
            $userId = $this->input('user_id');
            $grupo = $this->boolean('grupo');
            $quantidade = (int)$this->input('quantidade');

            if (!$data || !$horario || !$quantidade || !$userId) {
                return;
            }

            if ($grupo === false && $quantidade > 1) {
                $validator->errors()->add('quantidade', 'Somente grupos podem ter mais de uma pessoa na quantidade.');
            }

            if ($grupo === true && $quantidade < 10) {
                $validator->errors()->add('quantidade', 'O grupo precisa ter entre 10 e 50 pessoas ( incluindo voce).');
            }

            $dataAgendada = Carbon::parse($data)->startOfDay();
            $hoje = now()->startOfDay();
            $amanha = now()->addDay()->startOfDay();

            if ($dataAgendada->lt($hoje)) {
                $validator->errors()->add('data', 'Não é possível agendar em datas passadas.');

                return;
            }

            if ($dataAgendada->lt($amanha)) {
                $validator->errors()->add('data', 'A data do agendamento deve ser a partir de amanhã (mínimo de 1 dia de antecedência).');
            }

            if ($dataAgendada->isMonday()) {
                $validator->errors()->add('data', 'Não é possível agendar às segundas-feiras (bloqueio automático).');
            }

            $diaFechado = DiasFechados::whereDate('data', $dataAgendada)->exists();
            if ($diaFechado) {
                $validator->errors()->add('data', 'Não é possível agendar nesta data: marcada como dia bloqueado.');
            }

            $totalAgendado = Agendamento::whereDate('data', $dataAgendada)
                ->where('horario', $horario)
                ->sum('quantidade');

            if (($totalAgendado + $quantidade) > 50) {
                $disponiveis = max(0, 50 - $totalAgendado);
                $validator->errors()->add(
                    'quantidade',
                    "Capacidade excedida: já existem {$totalAgendado} pessoas para {$horario}. Restam apenas {$disponiveis} vagas."
                );
            }

            $horaAgendada = Carbon::createFromFormat('H:i', $horario);
            $inicioJanela = $horaAgendada->copy()->subMinutes(59)->format('H:i:s');
            $fimJanela = $horaAgendada->copy()->addMinutes(59)->format('H:i:s');

            $jaTemOutro = Agendamento::whereDate('data', $dataAgendada)
                ->where('user_id', $userId)
                ->whereBetween('horario', [$inicioJanela, $fimJanela])
                ->exists();

            if ($jaTemOutro) {
                $validator->errors()->add(
                    'horario',
                    'Você já possui um agendamento próximo neste dia. O intervalo mínimo entre agendamentos é de 1 hora.'
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
