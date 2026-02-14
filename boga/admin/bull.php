<?php
require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();
$pdo = pdo();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { redirect('/boga/admin/bulls.php'); }

$stmt = $pdo->prepare("
    SELECT b.*, c.name AS category_name
    FROM bulls b
    LEFT JOIN categories c ON c.id = b.category_id
    WHERE b.id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$bull = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$bull) { redirect('/boga/admin/bulls.php'); }

// Meydan listesi (güreş ekleme formu için)
$arenas = $pdo->query("SELECT id, name, city FROM arenas ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Bu boğanın güreşleri
$stmt = $pdo->prepare("
    SELECT bm.*, a.name AS arena_name, a.city AS arena_city
    FROM bull_matches bm
    LEFT JOIN arenas a ON a.id = bm.arena_id
    WHERE bm.bull_id = ?
    ORDER BY bm.match_date DESC, bm.id DESC
");
$stmt->execute([$id]);
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = 'Boğa Detayı';
$view  = __DIR__ . '/../views/admin_bull_detail.php';
include __DIR__ . '/../app/layout.php';
