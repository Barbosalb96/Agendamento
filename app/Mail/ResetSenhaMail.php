<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetSenhaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    public $token;

    public function __construct($email, $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    public function build()
    {
        $url = env('APP_URL_FRONT')."/resetar-senha?email={$this->email}&token={$this->token}";

        return $this->subject('Redefinição de Senha - Governo do Maranhão')
            ->view('emails.reset_senha')
            ->with([
                'url' => $url,
                'email' => $this->email,
            ]);
    }
}
