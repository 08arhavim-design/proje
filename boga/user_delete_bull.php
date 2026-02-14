<?php
declare(strict_types=1);
require_once __DIR__ . '/app/functions.php';
require_login();

$pdo = pdo();
$uid = current_user_id();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // GÜVENLİK: Sadece boğanın sahibi silebilir
    $stmt = $pdo->prepare("DELETE FROM bulls WHERE id = ? AND uid = ?");
    $stmt->execute([$id, $uid]);
    
    if($stmt->rowCount() > 0) {
        flash_set('ok', 'Kayıt başarıyla silindi.');
    }
}

redirect('user.php');