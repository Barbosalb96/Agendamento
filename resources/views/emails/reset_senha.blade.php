<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinição de Senha</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 480px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            padding: 32px 24px;
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
        }

        .button {
            display: block;
            width: 100%;
            background: #d72638;
            color: #fff;
            text-decoration: none;
            padding: 14px 0;
            border-radius: 6px;
            font-weight: bold;
            font-size: 1.1rem;
            margin: 32px 0 16px 0;
            text-align: center;
            transition: background 0.2s;
        }

        .button:hover {
            background: #b71c2b;
        }

        .footer {
            text-align: center;
            color: #888;
            font-size: 0.9rem;
            margin-top: 24px;
        }
    </style>
</head>
<body>

<div class="container">
    @include("emails.header")
    <h1>Redefinição de Senha</h1>
    <p>Olá,<br>Recebemos uma solicitação para redefinir a senha do seu acesso.<br><br>Basta clicar no botão abaixo para
        criar uma nova senha:</p>
    <a href="{{ $body["url"] }}" class="button">Redefinir Senha</a>
    <p style="font-size:0.95rem; color:#555; margin-top:16px;">Se você não solicitou a redefinição, ignore este
        e-mail.</p>
    <div class="footer">
        Governo do Maranhão<br>
        <span style="color:#d72638; font-weight:bold;">www.ma.gov.br</span>
    </div>
</div>
</body>
</html>
