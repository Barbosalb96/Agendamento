<?php

namespace App\SeatiMail\Transport;

use GuzzleHttp\ClientInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Throwable;

class SeatiMailTransport extends AbstractTransport
{
    protected ClientInterface $client;

    protected string $key;

    protected string $url;

    public function __construct(ClientInterface $client, $url, $key)
    {
        $this->key = $key;
        $this->client = $client;
        $this->url = $url;

        parent::__construct();
    }

    private function headers(): array
    {
        return [
            'Authorization' => "Basic {$this->key}",
            'Content-Type' => 'application/json',
        ];
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $arquivos = $email->getAttachments();

        try {
            $anexos = $this->prepareAttachments($arquivos);

            $this->client->request('POST', $this->url, [
                'headers' => $this->headers(),
                'json' => [
                    'destinatarios' => collect($email->getTo())->map->toString()->values()->all(),
                    'assunto' => $email->getSubject(),
                    'corpo' => $email->getHtmlBody() ?? $email->getTextBody(),
                    'anexo' => $anexos,
                ],
            ]);
        } catch (Throwable $throwable) {
            Log::error($throwable->getMessage());
        }
    }

    protected function prepareAttachments(array $attachments): array
    {
        $anexos = [];

        foreach ($attachments as $attachment) {
            // Verifique se é uma instância válida de DataPart
            if ($attachment instanceof \Symfony\Component\Mime\Part\DataPart) {

                $caminhoArquivo = $attachment->getBody();

                $anexos[] = [
                    'nome' => 'Carteira.pdf',
                    'conteudo' => base64_encode($caminhoArquivo),
                ];
            }
        }

        return $anexos;
    }

    public function __toString(): string
    {
        return 'seatimail';
    }
}
