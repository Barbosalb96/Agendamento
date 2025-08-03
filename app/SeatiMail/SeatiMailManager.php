<?php

namespace App\SeatiMail;

use App\SeatiMail\Transport\SeatiMailTransport;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Arr;

class SeatiMailManager extends MailManager
{
    protected function createSeatiMailTransport()
    {
        $config = $this->app['config']->get('services.seatimail', []);

        return new SeatiMailTransport(
            $this->guzzle($config),
            $config['url'],
            $config['key'],
        );
    }

    /**
     * Get a fresh Guzzle HTTP client instance.
     *
     * @return \GuzzleHttp\Client
     */
    protected function guzzle(array $config)
    {
        return new HttpClient(Arr::add(
            $config['guzzle'] ?? [],
            'connect_timeout',
            300
        ));
    }
}
