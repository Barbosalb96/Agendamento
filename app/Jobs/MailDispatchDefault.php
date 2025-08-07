<?php

namespace App\Jobs;

use App\Mail\DefaultMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class MailDispatchDefault implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $subject,
        private readonly array $body,
        private readonly string $view,
        private readonly string $to,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->to)->send(
            new DefaultMail($this->subject, $this->body, $this->view)
        );
    }
}
