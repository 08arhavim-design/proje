<?php
require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();
$pdo = pdo();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/boga/admin/bulls.php'); }

$bull_id   = isset($_POST['bull_id']) ? (int)$_POST['bull_id'] : 0;
$arena_id  = isset($_POST['arena_id']) ? (int)$_POST['arena_id'] : 0;
$match_date = trim($_POST['match_date'] ?? '');
$opponent  = trim($_POST['opponent'] ?? '');
$result    = trim($_POST['result'] ?? '');

// Basit doğrulama
if ($bull_id <= 0 || $arena_id <= 0 || $match_date === '') {
    redirect('/boga/admin/bull.php?id=' . (int)$bull_id);
}

// CSRF desteği varsa kontrol et (mevcut sistemde fonksiyon adı farklı olabilir)
if (function_exists('csrf_verify')) {
    csrf_verify($_POST['csrf_token'] ?? '');
}

// Insert
$stmt = $pdo->prepare("
    INSERT INTO bull_matches (bull_id, arena_id, match_date, opponent, result, created_at)
    VALUES (?, ?, ?, ?, ?, NOW())
");
$stmt->execute([$bull_id, $arena_id, $match_date, $opponent, $result]);

// Bulls tablosunda son güreş bilgilerini güncelle (opsiyonel)
$stmt = $pdo->prepare("SELECT name FROM arenas WHERE id=? LIMIT 1");
$stmt->execute([$arena_id]);
$a = $stmt->fetch(PDO::FETCH_ASSOC);
$last_arena = $a ? $a['name'] : null;

$stmt = $pdo->prepare("
    UPDATE bulls
    SET last_arena = COALESCE(?, last_arena),
        last_opponent = COALESCE(NULLIF(?, ''), last_opponent)
    WHERE id = ?
");
$stmt->execute([$last_arena, $opponent, $bull_id]);

redirect('/boga/admin/bull.php?id=' . (int)$bull_id);
