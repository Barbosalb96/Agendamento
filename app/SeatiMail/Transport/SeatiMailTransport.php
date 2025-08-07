<?php

namespace App\SeatiMail\Transport;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\Part\DataPart;
use Throwable;

/**
 * Transporte personalizado para envio de emails via API SeatiMail
 *
 * Implementa a interface de transporte do Symfony Mailer para integrar
 * com o serviço de email SeatiMail
 */
class SeatiMailTransport extends AbstractTransport
{
    /**
     * Cliente HTTP para fazer requisições à API
     */
    protected ClientInterface $client;

    /**
     * Chave de autenticação da API
     */
    protected string $key;

    /**
     * URL base da API SeatiMail
     */
    protected string $url;

    /**
     * Tamanho máximo permitido para anexos (em bytes)
     */
    protected int $maxAttachmentSize = 25 * 1024 * 1024; // 25MB

    /**
     * Inicializa o transporte SeatiMail
     *
     * @param  ClientInterface  $client  Cliente HTTP para requisições
     * @param  string  $url  URL da API SeatiMail
     * @param  string  $key  Chave de autenticação
     */
    public function __construct(ClientInterface $client, string $url, string $key)
    {
        if (empty($url) || empty($key)) {
            throw new \InvalidArgumentException('URL and key are required for SeatiMail transport');
        }

        $this->key = $key;
        $this->client = $client;
        $this->url = rtrim($url, '/');

        // Chama o construtor da classe pai
        parent::__construct();
    }

    /**
     * Prepara os cabeçalhos HTTP para as requisições à API
     *
     * @return array Cabeçalhos HTTP formatados
     */
    private function getHeaders(): array
    {
        return [
            'Authorization' => "Basic {$this->key}",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'SeatiMail-Laravel-Transport/1.0',
        ];
    }

    /**
     * Método principal para envio do email
     *
     * É chamado automaticamente pelo Symfony Mailer quando um email é enviado
     *
     * @param  SentMessage  $message  Mensagem a ser enviada
     *
     * @throws TransportException Se ocorrer erro no envio
     */
    protected function doSend(SentMessage $message): void
    {
        try {
            // Converte a mensagem Symfony para objeto Email
            $email = MessageConverter::toEmail($message->getOriginalMessage());

            // Valida se o email tem destinatários
            if (empty($email->getTo())) {
                throw new TransportException('Email must have at least one recipient');
            }

            // Prepara os dados para envio
            $payload = $this->preparePayload($email);

            // Envia a requisição para a API
            $this->sendRequest($payload);

            // Log de sucesso
            Log::info('Email sent successfully via SeatiMail', [
                'recipients' => count($payload['destinatarios']),
                'subject' => $payload['assunto'],
                'attachments' => count($payload['anexo']),
            ]);

        } catch (RequestException $e) {
            // Erro específico de requisição HTTP
            $error = 'SeatiMail API request failed: '.$e->getMessage();
            Log::error($error, ['response' => $e->getResponse()?->getBody()?->getContents()]);
            throw new TransportException($error, 0, $e);
        } catch (Throwable $e) {
            // Outros erros gerais
            $error = 'SeatiMail transport error: '.$e->getMessage();
            Log::error($error, ['exception' => $e]);
            throw new TransportException($error, 0, $e);
        }
    }

    /**
     * Prepara o payload (dados) para envio à API
     *
     * @param  Email  $email  Objeto email do Symfony
     * @return array Dados formatados para a API
     */
    protected function preparePayload(Email $email): array
    {
        return [
            'destinatarios' => $this->extractRecipients($email),
            'assunto' => $email->getSubject() ?: 'Sem assunto',
            'corpo' => $this->getEmailBody($email),
            'anexo' => $this->prepareAttachments($email->getAttachments()),
            'remetente' => $this->extractSender($email),
        ];
    }

    /**
     * Extrai os destinatários do email
     *
     * @return array Lista de endereços de email
     */
    protected function extractRecipients(Email $email): array
    {
        return collect($email->getTo())
            ->map(fn ($address) => $address->toString())
            ->values()
            ->all();
    }

    /**
     * Extrai o remetente do email
     *
     * @return string|null Endereço do remetente
     */
    protected function extractSender(Email $email): ?string
    {
        $from = $email->getFrom();

        return ! empty($from) ? $from[0]->toString() : null;
    }

    /**
     * Obtém o corpo do email, priorizando HTML sobre texto
     *
     * @return string Conteúdo do email
     */
    protected function getEmailBody(Email $email): string
    {
        // Prioriza HTML, mas usa texto se HTML não estiver disponível
        return $email->getHtmlBody() ?? $email->getTextBody() ?? '';
    }

    /**
     * Processa e prepara os anexos do email
     *
     * @param  array  $attachments  Lista de anexos do Symfony
     * @return array Anexos formatados para a API
     *
     * @throws TransportException Se anexo for muito grande
     */
    protected function prepareAttachments(array $attachments): array
    {
        $anexos = [];

        foreach ($attachments as $attachment) {
            // Verifica se é um anexo válido (DataPart)
            if (! $attachment instanceof DataPart) {
                Log::warning('Skipping invalid attachment type', [
                    'type' => get_class($attachment),
                ]);

                continue;
            }

            try {
                $anexo = $this->processAttachment($attachment);
                $anexos[] = $anexo;

            } catch (Throwable $e) {
                Log::error('Error processing attachment', [
                    'filename' => $attachment->getFilename(),
                    'error' => $e->getMessage(),
                ]);
                // Continua processando outros anexos
            }
        }

        return $anexos;
    }

    /**
     * Processa um anexo individual
     *
     * @return array Dados do anexo formatados
     *
     * @throws TransportException Se anexo for inválido
     */
    protected function processAttachment(DataPart $attachment): array
    {
        $content = $attachment->getBody();
        $filename = $attachment->getFilename() ?? 'attachment';

        // Verifica o tamanho do anexo
        if (strlen($content) > $this->maxAttachmentSize) {
            throw new TransportException(
                "Attachment '{$filename}' exceeds maximum size of ".
                ($this->maxAttachmentSize / 1024 / 1024).'MB'
            );
        }

        return [
            'nome' => $filename,
            'conteudo' => base64_encode($content),
            'tipo' => $attachment->getContentType() ?? 'application/octet-stream',
        ];
    }

    /**
     * Envia a requisição HTTP para a API SeatiMail
     *
     * @param  array  $payload  Dados a serem enviados
     *
     * @throws RequestException Se a requisição falhar
     */
    protected function sendRequest(array $payload): void
    {
        $response = $this->client->request('POST', $this->url, [
            'headers' => $this->getHeaders(),
            'json' => $payload,
            'timeout' => 60, // Timeout específico para esta requisição
        ]);

        // Verifica se a resposta indica sucesso
        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new RequestException(
                "API returned status code: {$statusCode}",
                $this->client->request('POST', $this->url)
            );
        }
    }

    /**
     * Retorna uma string identificadora do transporte
     *
     * Usado pelo Symfony Mailer para identificar o tipo de transporte
     *
     * @return string Nome do transporte
     */
    public function __toString(): string
    {
        return 'seatimail';
    }
}
