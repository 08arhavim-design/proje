<?php
declare(strict_types=1);
require_once __DIR__ . '/app/functions.php';
require_login();

$pdo = pdo();
$uid = current_user_id();

try {
    // Sadece giriş yapan kullanıcıya (uid) ait boğaları getiriyoruz
    $st = $pdo->prepare("SELECT * FROM bulls WHERE uid = ? ORDER BY id DESC");
    $st->execute([$uid]);
    $my_bulls = $st->fetchAll();
} catch (PDOException $e) {
    $my_bulls = [];
}

$title = 'Hesabım - Boğalarım';
$view = __DIR__ . '/views/user.php';
include __DIR__ . '/app/layout.php';