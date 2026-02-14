<?php
require_once __DIR__ . '/app/functions.php';

if (is_logged_in()) redirect('/');

if (empty($_SESSION['otp_user_id'])) {
  flash_set('err','OTP oturumu bulunamadı. Tekrar giriş yapın.');
  redirect('/');
}

$title = 'SMS doğrulama';
$view = __DIR__ . '/views/otp.php';
include __DIR__ . '/app/layout.php';
