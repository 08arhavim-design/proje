<?php
require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/admin/bulls_pending.php');
csrf_check();

$ids = $_POST['approve_ids'] ?? [];
$ids = array_values(array_filter(array_map('intval', (array)$ids), fn($v)=>$v>0));
if (!$ids) { flash_set('err','Seçim yapılmadı.'); redirect('/admin/bulls_pending.php'); }

$pdo = pdo();
$in = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("UPDATE bulls SET status='approved' WHERE id IN ($in)");
$stmt->execute($ids);

flash_set('ok','Seçilen boğalar onaylandı.');
redirect('/admin/bulls_pending.php');
