<?php
require_once __DIR__ . '/app/functions.php';

if (!is_logged_in()) {
    header('Location: ' . url('giris'));
    exit;
}

$pdo = pdo();
$current_user_id = current_user_id();

// Veritabanındaki mesaj sütununu bul (message mı body mi?)
$cols = $pdo->query("DESCRIBE messages")->fetchAll(PDO::FETCH_COLUMN);
$c_col = in_array('message', $cols) ? 'message' : (in_array('body', $cols) ? 'body' : null);

if (!$c_col) {
    die("Hata: Messages tablosunda mesaj içeriği için bir sütun (message veya body) bulunamadı.");
}

// Gelen Kutusu SQL Sorgusu (Sütun ismine göre dinamikleştirildi)
$sql = "
    SELECT 
        u.id, u.username, u.full_name,
        m.$c_col AS last_message, m.created_at, m.is_read, m.sender_id
    FROM (
        SELECT MAX(id) as last_id
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
        GROUP BY IF(sender_id = ?, receiver_id, sender_id)
    ) as last_msgs
    JOIN messages m ON m.id = last_msgs.last_id
    JOIN users u ON u.id = IF(m.sender_id = ?, m.receiver_id, m.sender_id)
    ORDER BY m.created_at DESC
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$current_user_id, $current_user_id, $current_user_id, $current_user_id]);
    $chat_list = $stmt->fetchAll();
} catch (PDOException $e) {
    die("SQL Hatası: " . $e->getMessage());
}

$title = 'Mesajlarım';
$view = __DIR__ . '/views/messages_view.php';
include __DIR__ . '/app/layout.php';