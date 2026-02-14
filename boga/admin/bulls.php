<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();
$pdo = pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bull_action'])) {
    csrf_check();
    $bid = (int)$_POST['bull_id'];
    $act = $_POST['bull_action'];
    $reason = trim((string)($_POST['reject_reason'] ?? ''));

    if ($act === 'approve') {
        // Onaylandığında sebebi temizler
        $pdo->prepare("UPDATE bulls SET status='approved', reject_reason=NULL WHERE id=?")->execute([$bid]);
    } elseif ($act === 'reject') {
        // Reddedildiğinde sebebi kaydeder
        $pdo->prepare("UPDATE bulls SET status='rejected', reject_reason=? WHERE id=?")->execute([$reason, $bid]);
    } elseif ($act === 'reset') {
        // Sıfırlandığında her şeyi temizler
        $pdo->prepare("UPDATE bulls SET status='pending', reject_reason=NULL WHERE id=?")->execute([$bid]);
    }
    redirect('admin/bulls.php');
}
$bulls = $pdo->query("SELECT * FROM bulls ORDER BY id DESC")->fetchAll();
$title = 'Boğa Yönetimi';
$view = __DIR__ . '/../views/admin_bulls.php';
include __DIR__ . '/../app/layout.php';