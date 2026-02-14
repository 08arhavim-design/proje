<?php
require_once __DIR__ . '/app/functions.php';

if (!is_logged_in()) {
    header('Location: ' . url('giris'));
    exit;
}

$pdo = pdo();
$current_user_id = current_user_id();
$target_user_id = isset($_GET['u']) ? (int)$_GET['u'] : 0;

if ($target_user_id <= 0 || $target_user_id === $current_user_id) {
    header('Location: ' . url(''));
    exit;
}

// 1. MESAJLARI OKUNDU OLARAK İŞARETLE (Bildirim sıfırlama)
$pdo->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0")
    ->execute([$target_user_id, $current_user_id]);

// Karşı kullanıcıyı çek
$stmt = $pdo->prepare("SELECT id, username, full_name, role FROM users WHERE id = ?");
$stmt->execute([$target_user_id]);
$peer = $stmt->fetch();

if (!$peer) { die("Kullanıcı bulunamadı."); }

// 2. MESAJ GÖNDERME İŞLEMİ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg_content = trim($_POST['message'] ?? ($_POST['body'] ?? ''));
    if (validate_csrf($_POST['csrf'] ?? '') && !empty($msg_content)) {
        // Tablo yapısını kontrol et ve kaydet
        $cols = $pdo->query("DESCRIBE messages")->fetchAll(PDO::FETCH_COLUMN);
        $content_col = in_array('message', $cols) ? 'message' : (in_array('body', $cols) ? 'body' : 'message');

        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, $content_col, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
        $stmt->execute([$current_user_id, $target_user_id, $msg_content]);
        header('Location: ' . current_full_url());
        exit;
    }
}

// 3. MESAJLARI ÇEKME
$stmt = $pdo->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
$stmt->execute([$current_user_id, $target_user_id, $target_user_id, $current_user_id]);
$msgs = $stmt->fetchAll();

$view = __DIR__ . '/views/dm.php';
include __DIR__ . '/app/layout.php';