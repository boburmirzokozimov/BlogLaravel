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
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.verify_subject', ['app' => config('app.name')]),
        );
    }

    public function content(): Content
    {
        // Force the root URL to ensure proper URL generation (especially in queue context)
        $appUrl = config('app.url');
        if ($appUrl) {
            URL::forceRootUrl($appUrl);
        }

        // Generate signed URL and ensure it's on a single line to prevent email client wrapping
        $verificationUrl = URL::signedRoute(
            'api.v1.email.verify',
            ['token' => $this->token],
            absolute: true
        );

        // Ensure the URL is properly formatted (fix any missing slashes)
        $verificationUrl = preg_replace('#(https?):([^/])#', '$1://$2', $verificationUrl);

        // Remove any potential line breaks that might cause issues in email clients
        $verificationUrl = str_replace(["\r", "\n", "\r\n"], '', $verificationUrl);

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
