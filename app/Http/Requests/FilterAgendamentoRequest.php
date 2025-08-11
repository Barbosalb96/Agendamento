<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Validation\Validator;

class FilterAgendamentoRequest extends BaseRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'data' => ['nullable', 'string'],
            'data_inicio' => ['nullable', 'string'],
            'data_fim' => ['nullable', 'string'],
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $v) {
            foreach (['data', 'data_inicio', 'data_fim'] as $campo) {
                if ($this->filled($campo) && ! $this->parseDate($this->input($campo))) {
                    $v->errors()->add($campo, 'Data inválida. Use Y-m-d, d-m-Y ou d/m/Y.');
                }
            }
        });
    }

    /** Normaliza para Y-m-d ou retorna null */
    public function parsed(string $key): ?string
    {
        $value = $this->input($key);
        if (! $value) {
            return null;
        }

        $d = $this->parseDate($value);

        return $d?->format('Y-m-d');
    }

    private function parseDate(string $value): ?Carbon
    {
        $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y'];
        foreach ($formats as $f) {
            try {
                return Carbon::createFromFormat($f, $value)->startOfDay();
            } catch (\Throwable) {
            }
        }

        return null;
    }
}
