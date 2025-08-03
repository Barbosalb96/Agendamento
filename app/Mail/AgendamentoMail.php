<?php

namespace App\Mail;

use App\Domains\Agendamento\Entities\Agendamento;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgendamentoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private Agendamento $agendamento)
    {
        //
    }

    public function build()
    {

        return $this->subject('Agendamento - Governo do Maranhão')
            ->view('emails.agendamento')
            ->with([
                'agendamento' => $this->agendamento,
            ]);
    }
}
