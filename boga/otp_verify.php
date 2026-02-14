<?php
require_once __DIR__ . '/app/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/otp.php');
csrf_check();

if (empty($_SESSION['otp_user_id']) || empty($_SESSION['otp_phone'])) {
  flash_set('err','OTP oturumu bulunamadı.');
  redirect('/');
}

$code = preg_replace('/\D+/', '', (string)($_POST['code'] ?? ''));
if (strlen($code) !== cfg()['app']['otp_digits']) {
  flash_set('err','Kod formatı hatalı.');
  redirect('/otp.php');
}

$pdo = pdo();
$uid = (int)$_SESSION['otp_user_id'];

// Rate limit for OTP verify
try {
  rate_limit_hit($pdo, 'otp:'.$_SESSION['otp_phone'], cfg()['app']['rate_limit_window_seconds'], cfg()['app']['rate_limit_max']);
} catch (Exception $ex) {
  flash_set('err', $ex->getMessage());
  redirect('/otp.php');
}

if (!otp_verify($pdo, $uid, $code, cfg()['app']['otp_max_attempts'])) {
  flash_set('err','Kod hatalı veya süresi doldu.');
  redirect('/otp.php');
}

$stmt = $pdo->prepare("SELECT id, username, phone, role, is_active FROM users WHERE id=? LIMIT 1");
$stmt->execute([$uid]);
$user = $stmt->fetch();
if (!$user || !(int)$user['is_active']) {
  flash_set('err','Kullanıcı pasif.');
  redirect('/');
}

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['username'] = (string)$user['username'];
$_SESSION['phone'] = (string)$user['phone'];
$_SESSION['role'] = (string)$user['role'];

unset($_SESSION['otp_user_id'], $_SESSION['otp_phone']);

if (in_array($_SESSION['role'], ['admin','superadmin'], true)) redirect('/admin/');
redirect('/user.php');
