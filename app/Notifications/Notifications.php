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
                'usuario' => $agendamento->user,
            ],
            'agendamento',
            $agendamento->user->email,
        ));
    }
}
