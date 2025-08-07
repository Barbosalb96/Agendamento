<?php

namespace App\SeatiMail;

use App\SeatiMail\Transport\SeatiMailTransport;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Mail\MailManager;

/**
 * Gerenciador personalizado de email para integração com SeatiMail
 *
 * Estende o MailManager do Laravel para adicionar suporte ao transporte SeatiMail
 */
class SeatiMailManager extends MailManager
{
    protected function createSeatiMailTransport(): SeatiMailTransport
    {
        $config = $this->app['config']->get('services.seatimail', []);

        // Valida se as configurações obrigatórias estão presentes
        $this->validateConfig($config);

        // Retorna uma nova instância do transporte SeatiMail
        return new SeatiMailTransport(
            $this->guzzle($config),  // Cliente HTTP configurado
            $config['url'],          // URL da API do SeatiMail
            $config['key'],          // Chave de autenticação
        );
    }

    /**
     * Valida se as configurações necessárias estão presentes
     *
     * @throws \Exception
     */
    protected function validateConfig(array $config): void
    {
        $required = ['url', 'key'];

        foreach ($required as $key) {
            if (empty($config[$key])) {
                throw new \Exception("SeatiMail configuration missing: {$key}");
            }
        }
    }

    /**
     * Cria uma instância configurada do cliente Guzzle HTTP
     *
     * @param  array  $config  Configurações do SeatiMail
     */
    protected function guzzle(array $config): HttpClient
    {
        // Configurações padrão para o cliente HTTP
        $defaultOptions = [
            'connect_timeout' => 30,    // Timeout de conexão em segundos
            'timeout' => 60,            // Timeout total da requisição
            'verify' => true,           // Verificar certificados SSL
            'http_errors' => true,      // Lançar exceções para erros HTTP
        ];

        // Mescla configurações personalizadas com as padrão
        $guzzleConfig = array_merge(
            $defaultOptions,
            $config['guzzle'] ?? []
        );

        return new HttpClient($guzzleConfig);
    }
}
