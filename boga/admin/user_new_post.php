<?php
require_once __DIR__ . '/../app/functions.php';
require_login();
require_superadmin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/admin/user_new.php');
csrf_check();

$username = trim((string)($_POST['username'] ?? ''));
$pass = (string)($_POST['password'] ?? '');
$phone = normalize_phone((string)($_POST['phone'] ?? ''));
$role = (string)($_POST['role'] ?? 'user');
$is_active = (int)($_POST['is_active'] ?? 1);

if ($username==='' || $pass==='' || $phone==='') { flash_set('err','Eksik alan.'); redirect('/admin/user_new.php'); }
if (!in_array($role, ['user','admin','superadmin'], true)) $role='user';

$pdo = pdo();
$hash = password_hash($pass, PASSWORD_DEFAULT);

try {
  $stmt = $pdo->prepare("INSERT INTO users(username,password,phone,role,is_active,created_at) VALUES(?,?,?,?,?,NOW())");
  $stmt->execute([$username,$hash,$phone,$role,$is_active]);
} catch (Exception $e) {
  flash_set('err','Kayıt yapılamadı (kullanıcı adı benzersiz olmalı).');
  redirect('/admin/user_new.php');
}

flash_set('ok','Kullanıcı oluşturuldu.');
redirect('/admin/users.php');
