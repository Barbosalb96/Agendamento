<?php

namespace App\Notifications;

use App\Jobs\MailDispatchDefault;

class Notifications
{
    public static function agendamento($agendamento)
    {
        dispatch(new MailDispatchDefault(
            'Agendamento - Governo do Maranhão',
            [
                'agendamento' => $agendamento,
                'usuario' => null, // No user relationship anymore
            ],
            'agendamento',
            $agendamento->email, // Use email directly from agendamento
        ));
    }
}
