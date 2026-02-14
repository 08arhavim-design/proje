<?php
require_once __DIR__ . '/app/functions.php';

if (!is_logged_in()) { redirect('giris'); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

$csrf = $_POST['csrf'] ?? '';
if (function_exists('verify_csrf')) {
  if (!verify_csrf($csrf)) die('CSRF Hatası!');
} else {
  if (!hash_equals(csrf_token(), (string)$csrf)) die('CSRF Hatası!');
}

$peerId = (int)($_POST['u'] ?? 0);
$body = trim((string)($_POST['body'] ?? ''));
$me = (int)current_user_id();

if ($peerId <= 0 || $peerId === $me || $body === '') {
  redirect('dm.php?u=' . $peerId);
}

$pdo = pdo();
$st = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, body) VALUES (?, ?, ?)");
$st->execute([$me, $peerId, $body]);

redirect('dm.php?u=' . $peerId);
