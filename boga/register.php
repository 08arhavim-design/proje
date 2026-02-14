<?php declare(strict_types=1);

require_once __DIR__ . '/app/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

/** VERIMOR */
$VERIMOR_USER = '908502426253';
$VERIMOR_PASS = 'KYV/224uns';
$VERIMOR_HEAD = '08502426253'; // onaylı header/source_addr (panelde neyse o)

/** OTP */
$OTP_TTL_SECONDS = 300; // 5 dk
$OTP_COOLDOWN    = 60;  // tekrar gönderme bekleme
$OTP_MAX_TRIES   = 6;

$pdo = pdo();
$error = '';
$ok = '';

/** register adımlarını sıfırla */
if (isset($_GET['new']) && $_GET['new'] === '1') {
    unset($_SESSION['reg_step'], $_SESSION['reg_pending'], $_SESSION['reg_otp']);
    $_SESSION['reg_step'] = 1;
}
$step = (int)($_SESSION['reg_step'] ?? 1);

function current_host_for_webotp(): string {
    $h = (string)($_SERVER['HTTP_HOST'] ?? 'ybglobal.com.tr');
    $h = preg_replace('/:\d+$/', '', $h);
    $h = strtolower(trim($h));
    return $h ?: 'ybglobal.com.tr';
}

function normalize_tr_msisdn(string $input): ?string {
    $digits = preg_replace('/\D+/', '', $input);
    if ($digits === null) return null;

    if (strlen($digits) === 11 && str_starts_with($digits, '05')) return '9' . $digits;   // 05.. -> 905..
    if (strlen($digits) === 10 && str_starts_with($digits, '5'))  return '90' . $digits;  // 5.. -> 905..
    if (strlen($digits) === 12 && str_starts_with($digits, '90')) return $digits;         // 90.. -> 90..
    return null;
}

function otp_generate(): string {
    return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function verimor_send_sms(string $apiUser, string $apiPass, string $sourceAddr, string $destMsisdn, string $message): array {
    $url = 'https://sms.verimor.com.tr/v2/send.json';
    $payload = [
        'username'    => $apiUser,
        'password'    => $apiPass,
        'source_addr' => $sourceAddr,
        'datacoding'  => 1,
        'messages'    => [
            ['dest' => $destMsisdn, 'msg' => $message]
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

    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resp === false) return ['ok' => false, 'http' => 0, 'raw' => '', 'error' => $err ?: 'cURL error'];
    $resp = trim((string)$resp);

    if ($code >= 200 && $code < 300) return ['ok' => true, 'http' => $code, 'raw' => $resp, 'error' => null];
    return ['ok' => false, 'http' => $code, 'raw' => $resp, 'error' => 'SMS gönderimi başarısız'];
}

/** users tablosu kolonları */
$cols = [];
foreach ($pdo->query("SHOW COLUMNS FROM users") as $c) {
    $cols[strtolower((string)$c['Field'])] = true;
}
$passCol = isset($cols['password_hash']) ? 'password_hash' : (isset($cols['password']) ? 'password' : '');

if ($passCol === '') {
    $error = 'users tablosunda şifre sütunu bulunamadı (password veya password_hash).';
}

/** phone/role kolonu yoksa ekle */
if ($passCol !== '' && !isset($cols['phone'])) {
    $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL");
    $cols['phone'] = true;
}
if ($passCol !== '' && !isset($cols['role'])) {
    $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'user'");
    $cols['role'] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $passCol !== '' && $error === '') {
    $action = (string)($_POST['action'] ?? '');

    // 1) OTP gönder
    if ($action === 'send_otp') {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $phoneRaw = trim((string)($_POST['phone'] ?? ''));

        if ($username === '' || $password === '' || $phoneRaw === '') {
            $error = 'Kullanıcı adı, şifre ve telefon zorunlu.';
        } else {
            $msisdn = normalize_tr_msisdn($phoneRaw);
            if (!$msisdn) {
                $error = 'Telefon formatı hatalı. Örn: 05xxxxxxxxx';
            } else {
                $st = $pdo->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
                $st->execute([':u' => $username]);
                if ($st->fetch()) {
                    $error = 'Bu kullanıcı adı zaten kayıtlı.';
                } else {
                    $st = $pdo->prepare("SELECT id FROM users WHERE phone = :p LIMIT 1");
                    $st->execute([':p' => $msisdn]);
                    if ($st->fetch()) {
                        $error = 'Bu telefon numarası zaten kayıtlı.';
                    } else {
                        $now = time();
                        $lastSend = (int)($_SESSION['reg_otp']['last_send_at'] ?? 0);

                        if ($lastSend > 0 && ($now - $lastSend) < $OTP_COOLDOWN) {
                            $error = 'Çok sık denediniz. Lütfen 1 dakika sonra tekrar deneyin.';
                        } else {
                            $code = otp_generate();
                            $_SESSION['reg_otp'] = [
                                'phone'       => $msisdn,
                                'hash'        => password_hash($code, PASSWORD_DEFAULT),
                                'expires_at'  => $now + $OTP_TTL_SECONDS,
                                'tries'       => 0,
                                'last_send_at'=> $now,
                            ];
                            $_SESSION['reg_pending'] = [
                                'username'  => $username,
                                'pass_hash' => password_hash($password, PASSWORD_DEFAULT),
                                'phone'     => $msisdn,
                            ];

                            // WebOTP formatı: @domain #kod
                            $host = current_host_for_webotp();
                            $msg  = "Boga Spor dogrulama kodunuz: {$code}\n@{$host} #{$code}";

                            $r = verimor_send_sms($VERIMOR_USER, $VERIMOR_PASS, $VERIMOR_HEAD, $msisdn, $msg);
                            if (!$r['ok']) {
                                $error = 'SMS gönderilemedi: ' . ($r['raw'] ?: $r['error']);
                            } else {
                                $_SESSION['reg_step'] = 2;
                                $step = 2;
                                $ok = 'Kod gönderildi. SMS ile gelen 6 haneli kodu girin.';
                            }
                        }
                    }
                }
            }
        }
    }

    // 2) OTP doğrula + kayıt oluştur
    if ($action === 'verify_otp') {
        $otp = trim((string)($_POST['otp'] ?? ''));
        $pending = $_SESSION['reg_pending'] ?? null;
        $rec = $_SESSION['reg_otp'] ?? null;

        if (!$pending || !$rec) {
            $error = 'Kayıt bilgileri bulunamadı. Lütfen tekrar deneyin.';
            $_SESSION['reg_step'] = 1;
            $step = 1;
        } else {
            if (time() > (int)$rec['expires_at']) {
                $error = 'Kodun süresi doldu. Tekrar SMS isteyin.';
            } else {
                $tries = (int)($rec['tries'] ?? 0);
                if ($tries >= $OTP_MAX_TRIES) {
                    $error = 'Çok fazla hatalı deneme. Tekrar SMS isteyin.';
                } else {
                    $_SESSION['reg_otp']['tries'] = $tries + 1;

                    if (!password_verify($otp, (string)$rec['hash'])) {
                        $error = 'Kod hatalı.';
                    } else {
                        $ins = $pdo->prepare("INSERT INTO users (username, {$passCol}, role, phone) VALUES (:u, :p, 'user', :ph)");
                        $ins->execute([
                            ':u'  => $pending['username'],
                            ':p'  => $pending['pass_hash'],
                            ':ph' => $pending['phone'],
                        ]);

                        unset($_SESSION['reg_pending'], $_SESSION['reg_otp']);
                        $_SESSION['reg_step'] = 3;
                        $step = 3;
                        $ok = 'Kayıt başarılı. Şimdi giriş yapabilirsiniz.';
                    }
                }
            }

            if ($step !== 3) {
                $_SESSION['reg_step'] = 2;
                $step = 2;
            }
        }
    }

    // 3) tekrar gönder
    if ($action === 'resend_otp') {
        $pending = $_SESSION['reg_pending'] ?? null;
        $rec = $_SESSION['reg_otp'] ?? null;

        if (!$pending || !$rec) {
            $error = 'Önce kayıt bilgilerini girin.';
            $_SESSION['reg_step'] = 1;
            $step = 1;
        } else {
            $now = time();
            $lastSend = (int)($rec['last_send_at'] ?? 0);

            if ($lastSend > 0 && ($now - $lastSend) < $OTP_COOLDOWN) {
                $error = 'Çok sık denediniz. Lütfen 1 dakika sonra tekrar deneyin.';
            } else {
                $code = otp_generate();
                $_SESSION['reg_otp'] = [
                    'phone'       => $pending['phone'],
                    'hash'        => password_hash($code, PASSWORD_DEFAULT),
                    'expires_at'  => $now + $OTP_TTL_SECONDS,
                    'tries'       => 0,
                    'last_send_at'=> $now,
                ];

                $host = current_host_for_webotp();
                $msg  = "Boga Spor dogrulama kodunuz: {$code}\n@{$host} #{$code}";

                $r = verimor_send_sms($VERIMOR_USER, $VERIMOR_PASS, $VERIMOR_HEAD, $pending['phone'], $msg);
                if (!$r['ok']) $error = 'SMS gönderilemedi: ' . ($r['raw'] ?: $r['error']);
                else $ok = 'Kod tekrar gönderildi.';
            }

            $_SESSION['reg_step'] = 2;
            $step = 2;
        }
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kayıt Ol</title>
<style>
body{margin:0;background:#0b1220;color:#e8eefc;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial}
.box{max-width:520px;margin:70px auto;padding:0 16px}
.card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.10);border-radius:16px;padding:22px}
h1{margin:0 0 12px;font-size:24px}
input{width:100%;padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(0,0,0,.25);color:#fff;margin:8px 0}
button{width:100%;padding:12px 14px;border:0;border-radius:12px;background:rgba(31,111,235,.35);color:#fff;font-weight:700;cursor:pointer}
.btn2{width:100%;padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#fff;font-weight:800;margin-top:8px;cursor:pointer}
.err{background:rgba(255,80,80,.12);border:1px solid rgba(255,80,80,.35);padding:10px 12px;border-radius:12px;margin-bottom:10px}
.ok{background:rgba(80,255,140,.10);border:1px solid rgba(80,255,140,.30);padding:10px 12px;border-radius:12px;margin-bottom:10px}
a{color:#9fc0ff;text-decoration:none}
.small{color:#8b949e;font-size:12px;line-height:1.4}
.linkbtn{display:block;text-align:center;padding:12px 14px;border-radius:12px;background:rgba(243,156,18,.18);border:1px solid rgba(243,156,18,.35);color:#f39c12;font-weight:900;text-decoration:none;margin-top:10px}
</style>
</head>
<body>
<div class="box">
  <div class="card">
    <h1>Kayıt ol</h1>

    <?php if ($error): ?><div class="err"><?php echo esc($error); ?></div><?php endif; ?>
    <?php if ($ok): ?><div class="ok"><?php echo esc($ok); ?></div><?php endif; ?>

    <?php if ($step === 1): ?>
      <form method="post" autocomplete="off">
        <input type="hidden" name="action" value="send_otp">
        <input name="username" placeholder="Kullanıcı adı" value="<?php echo esc($_POST['username'] ?? ''); ?>">
        <input name="phone" placeholder="Telefon (05xxxxxxxxx)" inputmode="tel" value="<?php echo esc($_POST['phone'] ?? ''); ?>">
        <input type="password" name="password" placeholder="Şifre">
        <button type="submit">SMS Kodu Gönder</button>
        <div class="small" style="margin-top:10px;">Telefon numaranı doğrulamak için SMS göndereceğiz.</div>
      </form>

    <?php elseif ($step === 2): ?>
      <form method="post" id="regOtpForm" autocomplete="off">
        <input type="hidden" name="action" value="verify_otp">
        <input id="regOtpInput" name="otp" placeholder="6 haneli kod" inputmode="numeric" autocomplete="one-time-code">
        <button type="submit">Kodu Doğrula ve Kaydı Tamamla</button>
      </form>

      <form method="post">
        <input type="hidden" name="action" value="resend_otp">
        <button class="btn2" type="submit">Kodu Tekrar Gönder</button>
      </form>

      <script>
        (async function () {
          if (!('OTPCredential' in window) || !navigator.credentials) return;
          try {
            const ac = new AbortController();
            setTimeout(() => ac.abort(), 60000);
            const otp = await navigator.credentials.get({
              otp: { transport: ['sms'] },
              signal: ac.signal
            });
            if (otp && otp.code) {
              document.getElementById('regOtpInput').value = otp.code;
              document.getElementById('regOtpForm').submit();
            }
          } catch (e) {}
        })();
      </script>

    <?php else: ?>
      <a class="linkbtn" href="/boga/login.php">Giriş Yap</a>
      <a class="linkbtn" href="/boga/register.php?new=1" style="margin-top:8px;background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.18);color:#fff;">
        Yeni Kayıt Aç
      </a>
      <div class="small" style="margin-top:10px;">Giriş yaptıktan sonra boğa kaydı ekleyebilirsiniz.</div>
    <?php endif; ?>

    <div style="margin-top:12px">
      <a href="/boga/login.php">Giriş yap</a> · <a href="/boga/">Ana sayfa</a>
    </div>
  </div>
</div>
</body>
</html>
