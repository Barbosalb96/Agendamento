<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Governamental - Estado do Maranhão</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .container {
            max-width: 800px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: #1e3c72;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .brasao {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #1e3c72;
            font-weight: bold;
        }

        .content {
            padding: 40px;
        }

        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 5px solid #ffc107;
        }

        .warning h2 {
            color: #856404;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .warning p {
            color: #856404;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .danger {
            background: #f8d7da;
            border: 1px solid #dc3545;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 5px solid #dc3545;
        }

        .danger h2 {
            color: #721c24;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .danger p {
            color: #721c24;
            line-height: 1.6;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 20px;
            border-left: 5px solid #17a2b8;
        }

        .info h2 {
            color: #0c5460;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .info p {
            color: #0c5460;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }

        .laws {
            list-style: none;
            padding-left: 20px;
        }

        .laws li {
            margin-bottom: 8px;
            position: relative;
        }

        .laws li::before {
            content: "§";
            position: absolute;
            left: -20px;
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="brasao">MA</div>
            <h1>Governo do Estado do Maranhão</h1>
            <p>Sistema de Agendamento Oficial</p>
        </div>

        <div class="content">
            <div class="warning">
                <h2>⚠️ Sistema Governamental Oficial</h2>
                <p>Este é um sistema oficial do <strong>Governo do Estado do Maranhão</strong>, destinado ao agendamento de serviços públicos.</p>
                <p>O acesso e uso deste sistema são monitorados e registrados conforme a legislação vigente.</p>
            </div>

            <div class="danger">
                <h2>🚨 Aviso Importante sobre Tentativas de Ataque</h2>
                <p><strong>ATENÇÃO:</strong> Tentativas de invasão, ataque, acesso não autorizado ou qualquer ação que comprometa a segurança deste sistema constituem <strong>CRIME</strong> e serão:</p>
                <ul class="laws">
                    <li><strong>Registradas automaticamente</strong> com identificação completa do usuário</li>
                    <li><strong>Reportadas às autoridades competentes</strong> (Polícia Civil e Federal)</li>
                    <li><strong>Processadas judicialmente</strong> nos termos da lei</li>
                    <li><strong>Sujeitas às penalidades</strong> previstas na legislação</li>
                </ul>
            </div>

            <div class="info">
                <h2>📋 Legislação Aplicável</h2>
                <p>Este sistema é protegido pelas seguintes leis:</p>
                <ul class="laws">
                    <li><strong>Lei nº 12.737/2012</strong> - Lei Carolina Dieckmann (Crimes Cibernéticos)</li>
                    <li><strong>Marco Civil da Internet</strong> - Lei nº 12.965/2014</li>
                    <li><strong>Lei Geral de Proteção de Dados</strong> - Lei nº 13.709/2018</li>
                    <li><strong>Código Penal Brasileiro</strong> - Arts. 266, 313-A, 313-B</li>
                </ul>
                <p><strong>Penalidades:</strong> Detenção de 3 meses a 1 ano, e multa, podendo chegar a 5 anos de reclusão em casos graves.</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} - Governo do Estado do Maranhão</p>
            <p>Sistema protegido por monitoramento 24/7 | Todos os acessos são registrados</p>
        </div>
    </div>
</body>
</html>