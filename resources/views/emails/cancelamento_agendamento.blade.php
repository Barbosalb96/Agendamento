<?php

use App\Helpers\QrCodeGenerator;
use Carbon\Carbon;

$uri = (new QrCodeGenerator())->generate($agendamento->uuid);

?>

    <!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelamento de Agendamento</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 480px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            padding: 32px 24px;
        }

        .header-logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .logo {
            height: 100px;
            object-fit: contain;
        }

        h1 {
            color: #d72638;
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 16px;
        }

        p {
            color: #222;
            font-size: 1rem;
            text-align: center;
            margin: 8px 0;
        }

        .qr-code {
            display: block;
            margin: 24px auto;
            width: 200px;
            height: 200px;
        }

        .footer {
            text-align: center;
            color: #888;
            font-size: 0.9rem;
            margin-top: 24px;
        }

        .footer span {
            color: #d72638;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header-logos">
        <img src="http://acervo.palaciodosleoes.ma.gov.br/wp-content/blogs.dir/110/files/2021/09/Ativo-1marca-1.png" alt="Palácio dos Leões" class="logo">
        <img src="https://portal.sei.ma.gov.br/wp-content/uploads/2023/07/logo-1.png" alt="Governo do Maranhão" class="logo">
    </div>

    <h1>Agendamento Cancelado</h1>
    <p>Olá <strong>{{ $agendamento->user->name }}</strong>,</p>
    <p>Informamos que o seu agendamento para visita ao Palácio dos Leões foi <strong>cancelado</strong>.</p>
    <p>Data do agendamento: <strong>{{ Carbon::parse($agendamento->data)->format('d/m/Y') }}</strong></p>
    <p>Horário: <strong>{{ Carbon::parse($agendamento->horario)->format('H:i') }}</strong></p>

    <img src="{{ $uri }}" alt="QR Code do agendamento cancelado" class="qr-code">

    <p style="color: #d72638; font-weight: bold; margin-top: 20px;">
        Este QR Code está invalidado e não permite acesso ao local.
    </p>

    <div class="footer">
        Governo do Maranhão<br>
        <span>www.ma.gov.br</span>
    </div>
</div>
</body>
</html>
