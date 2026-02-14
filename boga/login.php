<?php declare(strict_types=1);

require_once __DIR__ . '/app/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

/** VERIMOR */
$VERIMOR_USER = '908502426253';
$VERIMOR_PASS = 'KYV/224uns';
$VERIMOR_HEAD = '08502426253';

/** OTP */
$OTP_TTL_SECONDS = 300;
$OTP_COOLDOWN    = 60;
$OTP_MAX_TRIES   = 6;

$pdo = pdo();
$error = '';
$ok = '';

function current_host_for_webotp(): string {
    $h = (string)($_SERVER['HTTP_HOST'] ?? 'ybglobal.com.tr');
    $h = preg_replace('/:\d+$/', '', $h);
    $h = strtolower(trim($h));
    return $h ?: 'ybglobal.com.tr';
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

/** users kolon tespiti */
$cols = [];
foreach ($pdo->query("SHOW COLUMNS FROM users") as $c) {
    $cols[strtolower((string)$c['Field'])] = true;
}
$passCol = isset($cols['password_hash']) ? 'password_hash' : (isset($cols['password']) ? 'password' : '');

if ($passCol === '') $error = 'users tablosunda şifre sütunu bulunamadı.';
if (!isset($cols['phone'])) $error = 'users tablosunda phone sütunu yok (OTP için gerekli).';

if (isset($_GET['reset']) && $_GET['reset'] === '1') {
    unset($_SESSION['login_step'], $_SESSION['login_pending'], $_SESSION['login_otp']);
    $_SESSION['login_step'] = 1;
}

$step = (int)($_SESSION['login_step'] ?? 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error === '') {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'start') {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'Kullanıcı adı ve şifre zorunlu.';
        } else {
            $st = $pdo->prepare("SELECT id, username, role, phone, {$passCol} AS passval FROM users WHERE username = :u LIMIT 1");
            $st->execute([':u' => $username]);
            $u = $st->fetch(PDO::FETCH_ASSOC);

            if (!$u) {
                $error = 'Kullanıcı bulunamadı.';
            } else {
                $passval = (string)$u['passval'];
                $passOk = password_verify($password, $passval) || hash_equals($passval, $password);

                if (!$passOk) {
                    $error = 'Şifre hatalı.';
                } else {
                    $phone = trim((string)($u['phone'] ?? ''));
                    if ($phone === '') {
                        $error = 'Bu hesabın telefon numarası tanımlı değil.';
                    } else {
                        $now = time();
                        $lastSend = (int)($_SESSION['login_otp']['last_send_at'] ?? 0);

                        if ($lastSend > 0 && ($now - $lastSend) < $OTP_COOLDOWN) {
                            $error = 'Çok sık denediniz. Lütfen 1 dakika sonra tekrar deneyin.';
                        } else {
                            $code = otp_generate();

                            $_SESSION['login_otp'] = [
                                'phone'       => $phone,
                                'hash'        => password_hash($code, PASSWORD_DEFAULT),
                                'expires_at'  => $now + $OTP_TTL_SECONDS,
                                'tries'       => 0,
                                'last_send_at'=> $now,
                            ];
                            $_SESSION['login_pending'] = [
                                'user_id'  => (int)$u['id'],
                                'username' => (string)$u['username'],
                                'role'     => (string)($u['role'] ?? 'user'),
                            ];

                            $host = current_host_for_webotp();
                            $msg  = "Boga Spor giris kodunuz: {$code}\n@{$host} #{$code}";

                            $r = verimor_send_sms($VERIMOR_USER, $VERIMOR_PASS, $VERIMOR_HEAD, $phone, $msg);
                            if (!$r['ok']) {
                                $error = 'SMS gönderilemedi: ' . ($r['raw'] ?: $r['error']);
                            } else {
                                $_SESSION['login_step'] = 2;
                                $step = 2;
                                $ok = 'Kod gönderildi. SMS ile gelen 6 haneli kodu girin.';
                            }
                        }
                    }
                }
            }
        }
    }

    if ($action === 'verify') {
        $otp = trim((string)($_POST['otp'] ?? ''));
        $pending = $_SESSION['login_pending'] ?? null;
        $rec = $_SESSION['login_otp'] ?? null;

        if (!$pending || !$rec) {
            $error = 'Giriş bilgileri bulunamadı. Tekrar deneyin.';
            $_SESSION['login_step'] = 1;
            $step = 1;
        } else {
            if (time() > (int)$rec['expires_at']) {
                $error = 'Kodun süresi doldu. Tekrar giriş yapın.';
            } else {
                $tries = (int)($rec['tries'] ?? 0);
                if ($tries >= $OTP_MAX_TRIES) {
                    $error = 'Çok fazla hatalı deneme. Tekrar giriş yapın.';
                } else {
                    $_SESSION['login_otp']['tries'] = $tries + 1;

                    if (!password_verify($otp, (string)$rec['hash'])) {
                        $error = 'Kod hatalı.';
                    } else {
                        // Oturum değişkenleri (projene göre)
                        $_SESSION['user_id']  = (int)$pending['user_id'];
                        $_SESSION['uid']      = (int)$pending['user_id'];
                        $_SESSION['username'] = (string)$pending['username'];
                        $_SESSION['role']     = (string)$pending['role'];

                        unset($_SESSION['login_pending'], $_SESSION['login_otp'], $_SESSION['login_step']);

                        header('Location: /boga/user.php');
                        exit;
                    }
                }
            }

            $_SESSION['login_step'] = 2;
            $step = 2;
        }
    }

    if ($action === 'resend') {
        $pending = $_SESSION['login_pending'] ?? null;
        $rec = $_SESSION['login_otp'] ?? null;

        if (!$pending || !$rec) {
            $error = 'Önce kullanıcı adı/şifre ile giriş yapın.';
            $_SESSION['login_step'] = 1;
            $step = 1;
        } else {
            $now = time();
            $lastSend = (int)($rec['last_send_at'] ?? 0);

            if ($lastSend > 0 && ($now - $lastSend) < $OTP_COOLDOWN) {
                $error = 'Çok sık denediniz. Lütfen 1 dakika sonra tekrar deneyin.';
            } else {
                $code = otp_generate();

                $_SESSION['login_otp'] = [
                    'phone'       => (string)$rec['phone'],
                    'hash'        => password_hash($code, PASSWORD_DEFAULT),
                    'expires_at'  => $now + $OTP_TTL_SECONDS,
                    'tries'       => 0,
                    'last_send_at'=> $now,
                ];

                $host = current_host_for_webotp();
                $msg  = "Boga Spor giris kodunuz: {$code}\n@{$host} #{$code}";

                $r = verimor_send_sms($VERIMOR_USER, $VERIMOR_PASS, $VERIMOR_HEAD, (string)$rec['phone'], $msg);
                if (!$r['ok']) $error = 'SMS gönderilemedi: ' . ($r['raw'] ?: $r['error']);
                else $ok = 'Kod tekrar gönderildi.';
            }

            $_SESSION['login_step'] = 2;
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
<title>Giriş Yap</title>
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
</style>
</head>
<body>
<div class="box">
  <div class="card">
    <h1>Giriş yap</h1>

    <?php if ($error): ?><div class="err"><?php echo esc($error); ?></div><?php endif; ?>
    <?php if ($ok): ?><div class="ok"><?php echo esc($ok); ?></div><?php endif; ?>

    <?php if ($step === 1): ?>
      <form method="post" autocomplete="off">
        <input type="hidden" name="action" value="start">
        <input name="username" placeholder="Kullanıcı adı" value="<?php echo esc($_POST['username'] ?? ''); ?>">
        <input type="password" name="password" placeholder="Şifre">
        <button type="submit">Devam Et</button>
        <div class="small" style="margin-top:10px;">Şifre doğruysa SMS doğrulama kodu gönderilecektir.</div>
      </form>

    <?php else: ?>
      <form method="post" id="loginOtpForm" autocomplete="off">
        <input type="hidden" name="action" value="verify">
        <input id="loginOtpInput" name="otp" placeholder="6 haneli kod" inputmode="numeric" autocomplete="one-time-code">
        <button type="submit">Kodu Doğrula ve Giriş Yap</button>
      </form>

      <form method="post">
        <input type="hidden" name="action" value="resend">
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
              document.getElementById('loginOtpInput').value = otp.code;
              document.getElementById('loginOtpForm').submit();
            }
          } catch (e) {}
        })();
      </script>
    <?php endif; ?>

    <div style="margin-top:12px">
      <a href="/boga/register.php">Kayıt ol</a> · <a href="/boga/">Ana sayfa</a>
    </div>
  </div>
</div>
</body>
</html>
