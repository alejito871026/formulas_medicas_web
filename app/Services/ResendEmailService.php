<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ResendEmailService
{
    public function send(string|array $to, string $subject, string $html, ?string $text = null): void
    {
        $apiKey = config('services.resend.key');
        $from = config('mail.from.address');

        if (! is_string($apiKey) || trim($apiKey) === '' || ! is_string($from) || trim($from) === '') {
            throw new RuntimeException('email_provider_not_configured');
        }

        $payload = [
            'from' => $from,
            'to' => is_array($to) ? array_values($to) : [$to],
            'subject' => $subject,
            'html' => $html,
        ];

        if ($text !== null) {
            $payload['text'] = $text;
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->post('https://api.resend.com/emails', $payload);

        if ($response->failed()) {
            throw new RuntimeException(sprintf(
                'email_send_failed:%s:%s',
                $response->status(),
                $response->body()
            ));
        }
    }
}