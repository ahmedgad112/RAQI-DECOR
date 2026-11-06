<?php

namespace App\Services;

class WhatsAppLinkBuilder
{
    /**
     * Build a wa.me deep link with an optional prefilled message.
     */
    public function build(?string $phone, ?string $message = null): ?string
    {
        $digits = $this->normalizePhone($phone);

        if ($digits === null) {
            return null;
        }

        $url = 'https://wa.me/'.$digits;

        if ($message !== null && trim($message) !== '') {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }

    /**
     * Normalize phone numbers like 00966501094405 or +966 50 109 4405 to digits only.
     */
    public function normalizePhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        return $digits !== '' ? $digits : null;
    }

    /**
     * @param  array{name?: string|null, email?: string|null, phone?: string|null, subject?: string|null, message?: string|null}  $payload
     */
    public function messageFromContact(array $payload): string
    {
        $lines = [
            'رسالة من موقع راقي ديكور',
            '',
            'الاسم: '.($payload['name'] ?? '-'),
            'البريد: '.($payload['email'] ?? '-'),
            'الهاتف: '.($payload['phone'] ?? '-'),
            'الموضوع: '.($payload['subject'] ?? '-'),
            '',
            'الرسالة:',
            $payload['message'] ?? '-',
        ];

        return implode("\n", $lines);
    }
}
