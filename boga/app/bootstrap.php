<?php
declare(strict_types=1);

$config = require __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
  $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
  session_set_cookie_params([
    'lifetime' => 0,
    'path' => $config['app']['base_path'].'/',
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
  session_start();
}

date_default_timezone_set('Europe/Istanbul');

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function cfg(): array {
  static $c = null;
  if ($c === null) $c = require __DIR__ . '/config.php';
  return $c;
}

function pdo(): PDO {
  static $pdo = null;
  if ($pdo) return $pdo;
  $c = cfg()['db'];
  $dsn = "mysql:host={$c['host']};dbname={$c['name']};charset={$c['charset']}";
  $pdo = new PDO($dsn, $c['user'], $c['pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
  return $pdo;
}

function base_url(string $path=''): string {
  $bp = rtrim(cfg()['app']['base_path'], '/');
  return $bp . $path;
}

function csrf_token(): string {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['csrf'];
}

function csrf_check(): void {
  $t = $_POST['csrf'] ?? '';
  if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $t)) {
    http_response_code(400);
    exit('CSRF doğrulaması başarısız.');
  }
}

function redirect(string $path): void {
  header('Location: ' . base_url($path));
  exit;
}

function normalize_phone(string $p): string {
  $p = preg_replace('/\D+/', '', $p ?? '');
  if (str_starts_with($p, '0')) $p = '9'.$p; // 0xxxxxxxxxx -> 90xxxxxxxxxx
  if (str_starts_with($p, '90') && strlen($p) === 12) return $p;
  return $p;
}

function is_logged_in(): bool {
  return !empty($_SESSION['user_id']);
}

function require_login(): void {
  if (!is_logged_in()) redirect('/');
}

function require_admin(): void {
  if (empty($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','superadmin'], true)) {
    redirect('/');
  }
}

function require_superadmin(): void {
  if (empty($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    redirect('/');
  }
}

function flash_set(string $type, string $msg): void {
  $_SESSION['flash'] = ['type'=>$type, 'msg'=>$msg];
}
function flash_get(): ?array {
  if (empty($_SESSION['flash'])) return null;
  $f = $_SESSION['flash'];
  unset($_SESSION['flash']);
  return $f;
}
