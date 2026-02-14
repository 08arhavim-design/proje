<?php
declare(strict_types=1);

// Hataları görmek için açıyoruz
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();

$pdo = pdo();

// İŞLEM: Onay/Red butonuna basılırsa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bull_action'])) {
    csrf_check();
    $bid = (int)$_POST['bull_id'];
    $act = $_POST['bull_action'];
    $reason = trim((string)($_POST['reject_reason'] ?? ''));

    if ($act === 'approve') {
        $pdo->prepare("UPDATE bulls SET status='approved', reject_reason=NULL WHERE id=?")->execute([$bid]);
    } elseif ($act === 'reject') {
        $pdo->prepare("UPDATE bulls SET status='rejected', reject_reason=? WHERE id=?")->execute([$reason, $bid]);
    }
    redirect('admin/pending.php');
}

// VERİ ÇEKME: Burada bir debug (kontrol) yapıyoruz
$stmt = $pdo->prepare("SELECT * FROM bulls WHERE status = 'pending' ORDER BY id DESC");
$stmt->execute();
$bulls = $stmt->fetchAll();

// EĞER BURADA BOŞ DÖNÜYORSA AMA VERİTABANINDA VARSA:
// Aşağıdaki satırı geçici olarak aktif edip sayfayı yenileyerek veritabanını kontrol edebilirsin:
// print_r($pdo->query("SELECT id, name, status FROM bulls")->fetchAll()); die();

$title = 'Onay Bekleyenler';
$view  = __DIR__ . '/../views/admin_pending.php';
include __DIR__ . '/../app/layout.php';