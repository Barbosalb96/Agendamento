<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'O campo :attribute deve ser aceito.',
    'accepted_if' => 'O campo :attribute deve ser aceito quando :other for :value.',
    'active_url' => 'O campo :attribute deve conter uma URL válida.',
    'after' => 'O campo :attribute deve conter uma data posterior a :date.',
    'after_or_equal' => 'O campo :attribute deve conter uma data posterior ou igual a :date.',
    'alpha' => 'O campo :attribute deve conter apenas letras.',
    'alpha_dash' => 'O campo :attribute deve conter apenas letras, números, traços e sublinhados.',
    'alpha_num' => 'O campo :attribute deve conter apenas letras e números.',
    'array' => 'O campo :attribute deve conter um array.',
    'ascii' => 'O campo :attribute deve conter apenas caracteres alfanuméricos de byte único e símbolos.',
    'before' => 'O campo :attribute deve conter uma data anterior a :date.',
    'before_or_equal' => 'O campo :attribute deve conter uma data anterior ou igual a :date.',
    'between' => [
        'array' => 'O campo :attribute deve conter entre :min e :max itens.',
        'file' => 'O campo :attribute deve conter entre :min e :max kilobytes.',
        'numeric' => 'O campo :attribute deve conter um valor entre :min e :max.',
        'string' => 'O campo :attribute deve conter entre :min e :max caracteres.',
    ],
    'boolean' => 'O campo :attribute deve conter o valor verdadeiro ou falso.',
    'can' => 'O campo :attribute contém um valor não autorizado.',
    'confirmed' => 'A confirmação para o campo :attribute não confere.',
    'contains' => 'O campo :attribute está faltando um valor obrigatório.',
    'current_password' => 'A senha está incorreta.',
    'date' => 'O campo :attribute deve conter uma data válida.',
    'date_equals' => 'O campo :attribute deve conter uma data igual a :date.',
    'date_format' => 'O campo :attribute deve conter uma data no formato :format.',
    'decimal' => 'O campo :attribute deve conter :decimal casas decimais.',
    'declined' => 'O campo :attribute deve ser recusado.',
    'declined_if' => 'O campo :attribute deve ser recusado quando :other for :value.',
    'different' => 'Os campos :attribute e :other devem conter valores diferentes.',
    'digits' => 'O campo :attribute deve conter :digits dígitos.',
    'digits_between' => 'O campo :attribute deve conter entre :min e :max dígitos.',
    'dimensions' => 'O campo :attribute contém dimensões de imagem inválidas.',
    'distinct' => 'O campo :attribute contém um valor duplicado.',
    'doesnt_end_with' => 'O campo :attribute não pode terminar com um dos seguintes valores: :values.',
    'doesnt_start_with' => 'O campo :attribute não pode começar com um dos seguintes valores: :values.',
    'email' => 'O campo :attribute deve conter um endereço de email válido.',
    'ends_with' => 'O campo :attribute deve terminar com um dos seguintes valores: :values.',
    'enum' => 'O valor selecionado para :attribute é inválido.',
    'exists' => 'O valor selecionado para :attribute é inválido.',
    'extensions' => 'O campo :attribute deve conter um arquivo com uma das seguintes extensões: :values.',
    'file' => 'O campo :attribute deve conter um arquivo.',
    'filled' => 'O campo :attribute é obrigatório.',
    'gt' => [
        'array' => 'O campo :attribute deve conter mais de :value itens.',
        'file' => 'O campo :attribute deve conter mais de :value kilobytes.',
        'numeric' => 'O campo :attribute deve conter um valor maior que :value.',
        'string' => 'O campo :attribute deve conter mais de :value caracteres.',
    ],
    'gte' => [
        'array' => 'O campo :attribute deve conter :value itens ou mais.',
        'file' => 'O campo :attribute deve conter :value kilobytes ou mais.',
        'numeric' => 'O campo :attribute deve conter um valor maior ou igual a :value.',
        'string' => 'O campo :attribute deve conter :value caracteres ou mais.',
    ],
    'hex_color' => 'O campo :attribute deve conter uma cor hexadecimal válida.',
    'image' => 'O campo :attribute deve conter uma imagem.',
    'in' => 'O valor selecionado para :attribute é inválido.',
    'in_array' => 'O valor do campo :attribute não existe em :other.',
    'integer' => 'O campo :attribute deve conter um número inteiro.',
    'ip' => 'O campo :attribute deve conter um endereço de IP válido.',
    'ipv4' => 'O campo :attribute deve conter um endereço IPv4 válido.',
    'ipv6' => 'O campo :attribute deve conter um endereço IPv6 válido.',
    'json' => 'O campo :attribute deve conter uma string JSON válida.',
    'list' => 'O campo :attribute deve conter uma lista.',
    'lowercase' => 'O campo :attribute deve estar em minúsculas.',
    'lt' => [
        'array' => 'O campo :attribute deve conter menos de :value itens.',
        'file' => 'O campo :attribute deve conter menos de :value kilobytes.',
        'numeric' => 'O campo :attribute deve conter um valor menor que :value.',
        'string' => 'O campo :attribute deve conter menos de :value caracteres.',
    ],
    'lte' => [
        'array' => 'O campo :attribute não deve conter mais de :value itens.',
        'file' => 'O campo :attribute deve conter :value kilobytes ou menos.',
        'numeric' => 'O campo :attribute deve conter um valor menor ou igual a :value.',
        'string' => 'O campo :attribute deve conter :value caracteres ou menos.',
    ],
    'mac_address' => 'O campo :attribute deve conter um endereço MAC válido.',
    'max' => [
        'array' => 'O campo :attribute não deve conter mais de :max itens.',
        'file' => 'O campo :attribute não deve conter mais de :max kilobytes.',
        'numeric' => 'O campo :attribute não deve conter um valor maior que :max.',
        'string' => 'O campo :attribute não deve conter mais de :max caracteres.',
    ],
    'max_digits' => 'O campo :attribute não deve conter mais de :max dígitos.',
    'mimes' => 'O campo :attribute deve conter um arquivo do tipo: :values.',
    'mimetypes' => 'O campo :attribute deve conter um arquivo do tipo: :values.',
    'min' => [
        'array' => 'O campo :attribute deve conter pelo menos :min itens.',
        'file' => 'O campo :attribute deve conter pelo menos :min kilobytes.',
        'numeric' => 'O campo :attribute deve conter um valor de pelo menos :min.',
        'string' => 'O campo :attribute deve conter pelo menos :min caracteres.',
    ],
    'min_digits' => 'O campo :attribute deve conter pelo menos :min dígitos.',
    'missing' => 'O campo :attribute deve estar ausente.',
    'missing_if' => 'O campo :attribute deve estar ausente quando :other for :value.',
    'missing_unless' => 'O campo :attribute deve estar ausente a menos que :other seja :value.',
    'missing_with' => 'O campo :attribute deve estar ausente quando :values estiver presente.',
    'missing_with_all' => 'O campo :attribute deve estar ausente quando :values estiverem presentes.',
    'multiple_of' => 'O campo :attribute deve ser um múltiplo de :value.',
    'not_in' => 'O valor selecionado para :attribute é inválido.',
    'not_regex' => 'O formato do campo :attribute é inválido.',
    'numeric' => 'O campo :attribute deve conter um número.',
    'password' => [
        'letters' => 'O campo :attribute deve conter pelo menos uma letra.',
        'mixed' => 'O campo :attribute deve conter pelo menos uma letra maiúscula e uma minúscula.',
        'numbers' => 'O campo :attribute deve conter pelo menos um número.',
        'symbols' => 'O campo :attribute deve conter pelo menos um símbolo.',
        'uncompromised' => 'O :attribute fornecido apareceu em um vazamento de dados. Escolha um :attribute diferente.',
    ],
    'present' => 'O campo :attribute deve estar presente.',
    'present_if' => 'O campo :attribute deve estar presente quando :other for :value.',
    'present_unless' => 'O campo :attribute deve estar presente a menos que :other seja :value.',
    'present_with' => 'O campo :attribute deve estar presente quando :values estiver presente.',
    'present_with_all' => 'O campo :attribute deve estar presente quando :values estiverem presentes.',
    'prohibited' => 'O campo :attribute é proibido.',
    'prohibited_if' => 'O campo :attribute é proibido quando :other for :value.',
    'prohibited_unless' => 'O campo :attribute é proibido a menos que :other esteja em :values.',
    'prohibits' => 'O campo :attribute proíbe :other de estar presente.',
    'regex' => 'O formato do campo :attribute é inválido.',
    'required' => 'O campo :attribute é obrigatório.',
    'required_array_keys' => 'O campo :attribute deve conter entradas para: :values.',
    'required_if' => 'O campo :attribute é obrigatório quando :other for :value.',
    'required_if_accepted' => 'O campo :attribute é obrigatório quando :other for aceito.',
    'required_if_declined' => 'O campo :attribute é obrigatório quando :other for recusado.',
    'required_unless' => 'O campo :attribute é obrigatório a menos que :other esteja em :values.',
    'required_with' => 'O campo :attribute é obrigatório quando :values estiver presente.',
    'required_with_all' => 'O campo :attribute é obrigatório quando :values estiverem presentes.',
    'required_without' => 'O campo :attribute é obrigatório quando :values não estiver presente.',
    'required_without_all' => 'O campo :attribute é obrigatório quando nenhum dos :values estiverem presentes.',
    'same' => 'Os campos :attribute e :other devem conter valores iguais.',
    'size' => [
        'array' => 'O campo :attribute deve conter :size itens.',
        'file' => 'O campo :attribute deve conter :size kilobytes.',
        'numeric' => 'O campo :attribute deve conter o valor :size.',
        'string' => 'O campo :attribute deve conter :size caracteres.',
    ],
    'starts_with' => 'O campo :attribute deve começar com um dos seguintes valores: :values.',
    'string' => 'O campo :attribute deve ser uma string.',
    'timezone' => 'O campo :attribute deve conter um fuso horário válido.',
    'unique' => 'O valor do campo :attribute já está em uso.',
    'uploaded' => 'Falha no upload do campo :attribute.',
    'uppercase' => 'O campo :attribute deve estar em maiúsculas.',
    'url' => 'O campo :attribute deve conter uma URL válida.',
    'ulid' => 'O campo :attribute deve conter um ULID válido.',
    'uuid' => 'O campo :attribute deve conter um UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'data' => [
            'required' => 'A data do agendamento é obrigatória.',
            'date' => 'A data deve estar em um formato válido.',
            'after' => 'A data deve ser posterior a hoje.',
        ],
        'horario' => [
            'required' => 'O horário do agendamento é obrigatório.',
            'date_format' => 'O horário deve estar no formato HH:MM.',
        ],
        'quantidade' => [
            'required' => 'A quantidade de pessoas é obrigatória.',
            'integer' => 'A quantidade deve ser um número inteiro.',
            'min' => 'A quantidade deve ser pelo menos 1.',
            'max' => 'A quantidade não pode exceder 10 pessoas.',
        ],
        'email' => [
            'required' => 'O endereço de email é obrigatório.',
            'email' => 'O endereço de email deve ser válido.',
            'exists' => 'Este email não está cadastrado no sistema.',
        ],
        'password' => [
            'required' => 'A senha é obrigatória.',
            'min' => 'A senha deve ter pelo menos 8 caracteres.',
        ],
        'cpf' => [
            'required' => 'O CPF é obrigatório.',
            'unique' => 'Este CPF já está cadastrado no sistema.',
        ],
        'user_id' => [
            'exists' => 'Usuário não encontrado no sistema.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'nome',
        'username' => 'usuário',
        'email' => 'email',
        'password' => 'senha',
        'password_confirmation' => 'confirmação da senha',
        'city' => 'cidade',
        'country' => 'país',
        'address' => 'endereço',
        'phone' => 'telefone',
        'mobile' => 'celular',
        'age' => 'idade',
        'sex' => 'sexo',
        'gender' => 'gênero',
        'day' => 'dia',
        'month' => 'mês',
        'year' => 'ano',
        'hour' => 'hora',
        'minute' => 'minuto',
        'second' => 'segundo',
        'title' => 'título',
        'content' => 'conteúdo',
        'description' => 'descrição',
        'excerpt' => 'resumo',
        'date' => 'data',
        'time' => 'hora',
        'available' => 'disponível',
        'size' => 'tamanho',
        'data' => 'data',
        'horario' => 'horário',
        'quantidade' => 'quantidade',
        'observacao' => 'observação',
        'grupo' => 'grupo',
        'cpf' => 'CPF',
        'rg' => 'RG',
        'telefone' => 'telefone',
        'user_id' => 'usuário',
        'motivo' => 'motivo',
    ],
];
