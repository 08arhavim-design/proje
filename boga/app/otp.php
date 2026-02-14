<?php
// boga/app/otp.php
require_once __DIR__ . '/verimor_sms.php';

function otp_session_boot(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (!isset($_SESSION['otp'])) {
        $_SESSION['otp'] = [];
    }
}

function normalize_tr_msisdn(string $input): ?string
{
    $digits = preg_replace('/\D+/', '', $input);
    if ($digits === null) return null;

    // 05xxxxxxxxx -> 905xxxxxxxxx
    if (strlen($digits) === 11 && str_starts_with($digits, '05')) {
        return '9' . $digits;
    }

    // 5xxxxxxxxx -> 905xxxxxxxxx
    if (strlen($digits) === 10 && str_starts_with($digits, '5')) {
        return '90' . $digits;
    }

    // 905xxxxxxxxx
    if (strlen($digits) === 12 && str_starts_with($digits, '90')) {
        return $digits;
    }

    return null;
}

function otp_generate_code(): string
{
    return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function otp_can_send(string $msisdn): bool
{
    otp_session_boot();
    $now = time();

    $rec = $_SESSION['otp'][$msisdn] ?? null;
    if (!$rec) return true;

    // 60 sn cooldown
    if (!empty($rec['last_send_at']) && ($now - (int)$rec['last_send_at']) < 60) {
        return false;
    }

    // saatlik limit 5
    $hourAgo = $now - 3600;
    $sentLog = $rec['sent_log'] ?? [];
    $sentLog = array_values(array_filter($sentLog, fn($t) => (int)$t >= $hourAgo));
    $_SESSION['otp'][$msisdn]['sent_log'] = $sentLog;

    return count($sentLog) < 5;
}

function otp_send(string $apiUser, string $apiPass, string $sourceAddr, string $msisdn): array
{
    otp_session_boot();

    if (!otp_can_send($msisdn)) {
        return ['ok' => false, 'error' => 'Çok sık denediniz. Lütfen biraz bekleyip tekrar deneyin.'];
    }

    $code = otp_generate_code();
    $expiresAt = time() + 5 * 60; // 5 dk

    $_SESSION['otp'][$msisdn] = [
        'hash'        => password_hash($code, PASSWORD_DEFAULT),
        'expires_at'  => $expiresAt,
        'attempts'    => 0,
        'last_send_at'=> time(),
        'sent_log'    => array_merge($_SESSION['otp'][$msisdn]['sent_log'] ?? [], [time()]),
    ];

    $msg = "Boga Spor dogrulama kodunuz: {$code}\nKod 5 dakika gecerlidir.";
    $r = verimor_send_sms($apiUser, $apiPass, $sourceAddr, $msisdn, $msg);

    if (!$r['ok']) {
        return ['ok' => false, 'error' => 'SMS gönderilemedi: '.$r['raw']];
    }
    return ['ok' => true, 'error' => null];
}

function otp_verify(string $msisdn, string $code): array
{
    otp_session_boot();
    $rec = $_SESSION['otp'][$msisdn] ?? null;

    if (!$rec) return ['ok' => false, 'error' => 'Kod bulunamadı. Tekrar SMS isteyin.'];
    if (time() > (int)$rec['expires_at']) return ['ok' => false, 'error' => 'Kodun süresi doldu. Tekrar SMS isteyin.'];

    $attempts = (int)($rec['attempts'] ?? 0);
    if ($attempts >= 6) return ['ok' => false, 'error' => 'Çok fazla hatalı deneme. Tekrar SMS isteyin.'];

    $_SESSION['otp'][$msisdn]['attempts'] = $attempts + 1;

    if (!password_verify($code, (string)$rec['hash'])) {
        return ['ok' => false, 'error' => 'Kod hatalı.'];
    }

    // Başarılı
    unset($_SESSION['otp'][$msisdn]);
    return ['ok' => true, 'error' => null];
}
