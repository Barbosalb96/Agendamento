<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Messages Language Lines
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'failed' => 'Essas credenciais não coincidem com nossos registros.',
        'password' => 'A senha fornecida está incorreta.',
        'throttle' => 'Muitas tentativas de login. Tente novamente em :seconds segundos.',
        'unauthorized' => 'Você não tem autorização para acessar este recurso.',
        'unauthenticated' => 'Você precisa estar logado para acessar este recurso.',
        'login_success' => 'Login realizado com sucesso.',
        'logout_success' => 'Logout realizado com sucesso.',
        'token_expired' => 'Token de acesso expirado.',
        'token_invalid' => 'Token de acesso inválido.',
    ],

    'agendamento' => [
        'created' => 'Agendamento realizado com sucesso.',
        'updated' => 'Agendamento atualizado com sucesso.',
        'cancelled' => 'Agendamento cancelado com sucesso.',
        'not_found' => 'Agendamento não encontrado.',
        'conflict' => 'Conflito de horário. Este horário já está ocupado.',
        'no_slots' => 'Não há vagas disponíveis para este horário.',
        'past_date' => 'Não é possível agendar para uma data passada.',
        'monday_blocked' => 'Agendamentos não são permitidos às segundas-feiras.',
        'day_blocked' => 'Este dia está bloqueado para agendamentos.',
        'invalid_time' => 'Horário inválido para agendamento.',
        'max_capacity' => 'Capacidade máxima atingida para este horário.',
    ],

    'user' => [
        'created' => 'Usuário criado com sucesso.',
        'updated' => 'Usuário atualizado com sucesso.',
        'deleted' => 'Usuário removido com sucesso.',
        'not_found' => 'Usuário não encontrado.',
        'email_taken' => 'Este email já está em uso.',
        'cpf_taken' => 'Este CPF já está cadastrado.',
    ],

    'validation' => [
        'required_field' => 'Este campo é obrigatório.',
        'invalid_format' => 'Formato inválido.',
        'min_length' => 'Este campo deve ter pelo menos :min caracteres.',
        'max_length' => 'Este campo não pode ter mais de :max caracteres.',
        'email_invalid' => 'Endereço de email inválido.',
        'date_invalid' => 'Data inválida.',
        'numeric_only' => 'Este campo deve conter apenas números.',
    ],

    'system' => [
        'error_500' => 'Erro interno do servidor. Tente novamente mais tarde.',
        'error_404' => 'Recurso não encontrado.',
        'error_403' => 'Acesso negado.',
        'error_401' => 'Não autorizado.',
        'error_422' => 'Dados de entrada inválidos.',
        'maintenance' => 'Sistema em manutenção. Tente novamente mais tarde.',
        'success' => 'Operação realizada com sucesso.',
        'updated' => 'Atualização realizada com sucesso.',
        'deleted' => 'Removido com sucesso.',
    ],

    'email' => [
        'sent' => 'Email enviado com sucesso.',
        'failed' => 'Falha no envio do email.',
        'reset_password_subject' => 'Redefinir Senha - Sistema de Agendamento',
        'appointment_confirmation_subject' => 'Confirmação de Agendamento',
        'appointment_cancellation_subject' => 'Cancelamento de Agendamento',
    ],

    'general' => [
        'yes' => 'Sim',
        'no' => 'Não',
        'active' => 'Ativo',
        'inactive' => 'Inativo',
        'enabled' => 'Habilitado',
        'disabled' => 'Desabilitado',
        'save' => 'Salvar',
        'cancel' => 'Cancelar',
        'delete' => 'Excluir',
        'edit' => 'Editar',
        'view' => 'Visualizar',
        'loading' => 'Carregando...',
        'search' => 'Buscar',
        'filter' => 'Filtrar',
        'clear' => 'Limpar',
        'confirm' => 'Confirmar',
        'continue' => 'Continuar',
        'back' => 'Voltar',
    ],
];