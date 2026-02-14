<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();
$pdo = pdo();

// Sadece onay bekleyenleri çekiyoruz
$bulls = $pdo->query("SELECT * FROM bulls WHERE status = 'pending' ORDER BY id DESC")->fetchAll();

$title = 'Onay Bekleyen Boğalar';
$view = __DIR__ . '/../views/admin_bulls_pending.php';
include __DIR__ . '/../app/layout.php';