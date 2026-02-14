<?php
// File: /boga/app/online_list.php
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_logged_in()) {
    echo json_encode(['ok' => true, 'users' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = pdo();
    $me = (int) current_user_id();

    $st = $pdo->prepare("
        SELECT
            u.id,
            u.username,
            u.full_name,
            u.role,
            p.last_seen
        FROM user_presence p
        JOIN users u ON u.id = p.user_id
        WHERE p.last_seen >= (NOW() - INTERVAL 10 MINUTE)
          AND u.id <> ?
        ORDER BY p.last_seen DESC
        LIMIT 24
    ");
    $st->execute([$me]);

    echo json_encode(
        ['ok' => true, 'users' => $st->fetchAll(PDO::FETCH_ASSOC)],
        JSON_UNESCAPED_UNICODE
    );
} catch (Throwable $e) {
    echo json_encode(['ok' => false, 'users' => []], JSON_UNESCAPED_UNICODE);
}
