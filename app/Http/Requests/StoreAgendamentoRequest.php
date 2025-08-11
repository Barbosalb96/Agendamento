<?php

namespace App\Http\Requests;

use App\Domains\Agendamento\Entities\Agendamento;
use App\Models\DiasFechados;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAgendamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
            ],
            'cpf' => [
                'required',
                'regex:/^\d{11}$/',
                'cpf',
            ],
            'rg' => ['required', 'string', 'max:20', 'regex:/^[0-9.\-Xx]+$/'],
            'telefone' => [
                'required',
                'digits_between:10,11',
            ],
            'nacionalidade' => ['required', Rule::in(['brasileiro', 'estrangeiro'])],
            'nacionalidade_grupo' => ['required', Rule::in(['brasileiro', 'estrangeiro'])],
            'deficiencia' => ['sometimes', 'boolean'],

            'data' => ['required', 'date', 'after_or_equal:tomorrow'],
            'horario' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    try {
                        $time = Carbon::createFromFormat('H:i', $value);
                    } catch (\Throwable $e) {
                        $fail('Horário inválido. Use o formato HH:mm.');

                        return;
                    }

                    if ($time->minute !== 0) {
                        $fail('Só é permitido agendar em horários cheios (ex.: 09:00, 10:00, 11:00).');

                        return;
                    }

                    $hour = (int) $time->format('H');
                    if ($hour < 9 || $hour > 17) {
                        $fail('Os horários disponíveis são apenas entre 09:00 e 17:00, em intervalos de 1 hora.');
                    }
                },
            ],

            'grupo' => ['sometimes', 'boolean'],
            'observacao' => ['sometimes', 'nullable', 'string'],
            'quantidade' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $input = $this->all();

        if (isset($input['cpf'])) {
            $input['cpf'] = preg_replace('/\D+/', '', $input['cpf']);
        }
        if (isset($input['telefone'])) {
            $input['telefone'] = preg_replace('/\D+/', '', $input['telefone']);
        }

        if (! empty($input['horario'])) {
            try {
                $input['horario'] = Carbon::parse($input['horario'])->format('H:i');
            } catch (\Throwable $e) {
            }
        }

        $user = $this->user();
        if ($user && method_exists($user, 'isAdmin') && ! $user->isAdmin()) {
            $input['user_id'] = $user->id;
        }

        $this->replace($input);
    }

    public function withValidator($validator)
    {
        /** @var Validator $validator */
        $validator->after(function (Validator $validator) {
            $data = $this->input('data');
            $horario = $this->input('horario');
            $email = $this->input('email');
            $cpf = $this->input('cpf');
            $grupo = $this->boolean('grupo');
            $quantidade = (int) $this->input('quantidade');

            if (! $data || ! $horario || ! $quantidade) {
                return;
            }

            if ($grupo === false && $quantidade > 1) {
                $validator->errors()->add('quantidade', 'Para agendar mais de uma pessoa, selecione a opção "Agendamento em grupo".');
            }
            if ($grupo === true && $quantidade < 10) {
                $validator->errors()->add('quantidade', 'Agendamentos em grupo devem ter no mínimo 10 pessoas.');
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

            $jaTemOutroQuery = Agendamento::whereDate('data', $dataAgendada)
                ->whereBetween('horario', [$inicioJanela, $fimJanela]);

            if (! empty($email)) {
                $jaTemOutroQuery->where('email', $email);
            } elseif (! empty($cpf)) {
                $jaTemOutroQuery->where('cpf', $cpf);
            }

            if ($jaTemOutroQuery->exists()) {
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
            // Nome
            'nome.required' => 'Informe seu nome completo.',
            'nome.string' => 'O nome deve conter apenas caracteres válidos.',
            'nome.max' => 'O nome não pode ter mais de 255 caracteres.',

            // E-mail
            'email.required' => 'Informe um e-mail válido para contato.',
            'email.email' => 'O formato do e-mail está incorreto.',

            // CPF
            'cpf.required' => 'O CPF é obrigatório para o agendamento.',
            'cpf.regex' => 'O CPF deve ter exatamente 11 dígitos (apenas números).',
            'cpf.cpf' => 'O CPF informado não é válido.',

            // RG
            'rg.required' => 'O número do RG é obrigatório.',
            'rg.string' => 'O RG deve ser um texto válido.',
            'rg.max' => 'O RG não pode ter mais de 20 caracteres.',
            'rg.regex' => 'O RG deve conter apenas números, pontos, hífens ou X.',

            // Telefone
            'telefone.required' => 'Informe um telefone para contato.',
            'telefone.digits_between' => 'O telefone deve ter entre 10 e 11 dígitos.',

            // Nacionalidade
            'nacionalidade.required' => 'Selecione sua nacionalidade.',
            'nacionalidade.in' => 'A nacionalidade deve ser "brasileiro" ou "estrangeiro".',
            'nacionalidade_grupo.required' => 'Selecione a nacionalidade do grupo.',
            'nacionalidade_grupo.in' => 'A nacionalidade do grupo deve ser "brasileiro" ou "estrangeiro".',

            // Deficiência
            'deficiencia.boolean' => 'Informe se possui deficiência (sim ou não).',

            // Data
            'data.required' => 'Selecione a data desejada para o agendamento.',
            'data.date' => 'A data informada não é válida.',
            'data.after_or_equal' => 'O agendamento deve ser feito com pelo menos 1 dia de antecedência.',

            // Horário
            'horario.required' => 'Selecione o horário desejado.',
            'horario.date_format' => 'O horário deve estar no formato HH:MM (exemplo: 14:00).',

            // Grupo
            'grupo.boolean' => 'Informe se é um agendamento individual ou em grupo.',

            // Observação
            'observacao.string' => 'As observações devem ser um texto válido.',

            // Quantidade
            'quantidade.required' => 'Informe quantas pessoas participarão.',
            'quantidade.integer' => 'A quantidade deve ser um número válido.',
            'quantidade.min' => 'É necessário pelo menos 1 pessoa.',
            'quantidade.max' => 'O máximo permitido é 50 pessoas por agendamento.',

        ];
    }
}
