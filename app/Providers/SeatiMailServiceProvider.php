<?php

namespace App\Providers;

use App\SeatiMail\SeatiMailManager;
use Illuminate\Mail\MailServiceProvider;

class SeatiMailServiceProvider extends MailServiceProvider
{
    /**
     * Register the Illuminate mailer instance.
     *
     * @return void
     */
    protected function registerIlluminateMailer()
    {
        $this->app->singleton('mail.manager', function ($app) {
            return new SeatiMailManager($app);
        });

        $this->app->bind('mailer', function ($app) {
            return $app->make('mail.manager')->mailer();
        });
    }
}
