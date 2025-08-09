@include('emails.header')

<div style="background-color: #f8f9fa; padding: 20px; margin: 20px 0; border-left: 5px solid #dc3545;">
    <h2 style="color: #dc3545; margin-bottom: 20px;">🚨 Erro no Sistema - Governo do Maranhão</h2>
    
    <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3 style="color: #333; margin-bottom: 15px;">📊 Informações do Erro</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 8px; font-weight: bold; width: 120px;">Data/Hora:</td>
                <td style="padding: 8px;">{{ $body['timestamp'] ?? now()->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 8px; font-weight: bold;">Servidor:</td>
                <td style="padding: 8px;">{{ $body['server'] ?? 'N/A' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 8px; font-weight: bold;">URL:</td>
                <td style="padding: 8px; word-break: break-all;">{{ $body['error']['url'] }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 8px; font-weight: bold;">Método:</td>
                <td style="padding: 8px;">{{ $body['error']['method'] }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 8px; font-weight: bold;">IP Cliente:</td>
                <td style="padding: 8px;">{{ $body['error']['ip'] }}</td>
            </tr>
        </table>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3 style="color: #333; margin-bottom: 15px;">⚠️ Detalhes do Erro</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 8px; font-weight: bold; width: 120px;">Mensagem:</td>
                <td style="padding: 8px; color: #dc3545; font-weight: 500;">{{ $body['error']['message'] }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 8px; font-weight: bold;">Arquivo:</td>
                <td style="padding: 8px; font-family: monospace; font-size: 12px;">{{ $body['error']['file'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; font-weight: bold;">Linha:</td>
                <td style="padding: 8px; font-family: monospace;">{{ $body['error']['line'] }}</td>
            </tr>
        </table>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px;">
        <h3 style="color: #333; margin-bottom: 15px;">🔍 Stack Trace</h3>
        <pre style="background: #f8f9fa; padding: 15px; border-radius: 4px; font-size: 11px; line-height: 1.4; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; max-height: 400px; overflow-y: auto;">{{ $body['error']['trace'] }}</pre>
    </div>

    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 20px; border-left: 5px solid #ffc107;">
        <p style="margin: 0; color: #856404; font-weight: 500;">
            ⚡ Este erro foi capturado automaticamente pelo sistema de monitoramento do Governo do Maranhão.
        </p>
        <p style="margin: 8px 0 0 0; color: #856404; font-size: 14px;">
            Para mais informações, verifique os logs do servidor ou entre em contato com a equipe técnica.
        </p>
    </div>
</div>
