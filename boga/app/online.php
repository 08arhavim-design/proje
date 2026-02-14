<?php
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_logged_in()) {
    echo json_encode(['ok' => true, 'logged_in' => false]);
    exit;
}

try {
    $pdo = pdo();
    $uid = (int)current_user_id();

    $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
    $ua = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 250);

    $st = $pdo->prepare("
        INSERT INTO user_presence (user_id, last_seen, ip, user_agent)
        VALUES (?, NOW(), ?, ?)
        ON DUPLICATE KEY UPDATE last_seen=NOW(), ip=VALUES(ip), user_agent=VALUES(user_agent)
    ");
    $st->execute([$uid, $ip, $ua]);

    echo json_encode(['ok' => true, 'logged_in' => true]);
} catch (Throwable $e) {
    echo json_encode(['ok' => false]);
}