<?php

declare(strict_types=1);

namespace App\Application\Mail;

use App\Domain\User\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $token
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.verify_subject', ['app' => config('app.name')]),
        );
    }

    public function content(): Content
    {
        $verificationUrl = URL::signedRoute(
            'api.v1.email.verify',
            ['token' => $this->token]
        );

        return new Content(
            view: 'emails.verify',
            with: [
                'userName' => $this->user->name(),
                'verificationUrl' => $verificationUrl,
                'appName' => config('app.name'),
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function attachments(): array
    {
        return [];
    }
}
