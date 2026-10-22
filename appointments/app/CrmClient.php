<?php
declare(strict_types=1);

final class CrmClient
{
    public function deliver(array $booking): void
    {
        $enabledByDefault = env('APP_ENV', 'local') === 'production' ? 'true' : 'false';
        if (env('CRM_SYNC_ENABLED', $enabledByDefault) !== 'true') {
            return;
        }

        $url = env('CRM_WEBHOOK_URL');
        $clientKey = env('CRM_CLIENT_KEY');
        $secret = env('CRM_WEBHOOK_SECRET');
        if ($url === '' || $clientKey === '' || $secret === '') {
            throw new RuntimeException('CRM integration is not configured.');
        }
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true) || (env('APP_ENV', 'local') === 'production' && $scheme !== 'https')) {
            throw new RuntimeException('CRM webhook URL is invalid.');
        }

        $symptoms = json_decode((string) ($booking['symptoms'] ?? '[]'), true);
        $attribution = json_decode((string) ($booking['attribution'] ?? '{}'), true);
        $payload = json_encode([
            'submission_id' => $booking['public_reference'],
            'name' => $booking['name'],
            'phone' => $booking['phone'],
            'branch' => $booking['branch'],
            'city' => $booking['city'],
            'preferred_appointment_date' => $booking['preferred_date'],
            'treatment' => 'Varicose veins treatment appointment',
            'symptoms' => is_array($symptoms) ? array_values($symptoms) : [],
            'other_symptom' => $booking['other_symptom'] ?: null,
            'landing_page' => $booking['landing_page'],
            'attribution' => is_array($attribution) ? $attribution : [],
            'consent' => true,
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $curl = curl_init($url);
        if ($curl === false) {
            throw new RuntimeException('CRM delivery could not start.');
        }
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'X-CRM-Client-Key: ' . $clientKey,
                'X-CRM-Signature: sha256=' . hash_hmac('sha256', $payload, $secret),
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        $response = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $failed = $response === false || $status < 200 || $status >= 300;
        curl_close($curl);
        if ($failed) {
            error_log('[havisha] CRM webhook delivery failed; status=' . $status);
            throw new RuntimeException('CRM delivery failed.');
        }
    }
}
