<h2>🚨 Erro no Servidor</h2>
<p><strong>URL:</strong> {{ $body['error']['url'] }}</p>
<p><strong>Método:</strong> {{  $body['error']['method'] }}</p>
<p><strong>IP:</strong> {{  $body['error']['ip'] }}</p>
<p><strong>Mensagem:</strong> {{ $body['error']['message']  }}</p>
<p><strong>Arquivo:</strong> {{ $body['error']['file'] }}</p>
<p><strong>Linha:</strong> {{ $body['error']['line'] }}</p>

<h3>Trace:</h3>
<pre>{{ $body["error"]["trace"] }}</pre>
