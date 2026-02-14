<?php
// boga/app/verimor_sms.php

function verimor_send_sms(string $apiUser, string $apiPass, string $sourceAddr, string $destMsisdn, string $message): array
{
    // Verimor SMS API v2 - POST /v2/send.json
    // Doküman: https://sms.verimor.com.tr/v2/send.json :contentReference[oaicite:1]{index=1}

    $url = 'https://sms.verimor.com.tr/v2/send.json';

    $payload = [
        'username'     => $apiUser,
        'password'     => $apiPass,
        'source_addr'  => $sourceAddr, // OİM’de tanımlı başlık olmalı
        'datacoding'   => 1,           // 1: GSM Turkish (Türkçe karakter için) :contentReference[oaicite:2]{index=2}
        'messages'     => [
            [
                'dest' => $destMsisdn, // örn: 905xxxxxxxxx
                'msg'  => $message,
            ]
        ],
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT        => 20,
    ]);

    $respBody = curl_exec($ch);
    $err      = curl_error($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($respBody === false) {
        return ['ok' => false, 'http' => 0, 'raw' => '', 'error' => $err ?: 'cURL error'];
    }

    // Başarılı yanıtta plain text bir kampanya id dönebiliyor. :contentReference[oaicite:3]{index=3}
    $respBody = trim((string)$respBody);

    if ($httpCode >= 200 && $httpCode < 300) {
        return ['ok' => true, 'http' => $httpCode, 'raw' => $respBody, 'error' => null];
    }

    return ['ok' => false, 'http' => $httpCode, 'raw' => $respBody, 'error' => 'Verimor SMS gönderimi başarısız'];
}
