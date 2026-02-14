<?php
require_once __DIR__ . '/functions.php';

if (!is_logged_in()) {
    redirect('giris');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$csrf = (string)($_POST['csrf'] ?? '');
if (function_exists('verify_csrf')) {
    if (!verify_csrf($csrf)) {
        exit('CSRF Hatası!');
    }
} else {
    if (!hash_equals(csrf_token(), $csrf)) {
        exit('CSRF Hatası!');
    }
}

$action   = (string)($_POST['action'] ?? '');
$targetId = (int)($_POST['user_id'] ?? 0);
$me       = (int)current_user_id();

if ($targetId <= 0 || $targetId === $me) {
    redirect('');
}

try {
    $pdo = pdo();

    if ($action === 'add') {
        $st = $pdo->prepare("INSERT IGNORE INTO friends (requester_id, addressee_id, status) VALUES (?, ?, 'pending')");
        $st->execute([$me, $targetId]);
    } elseif ($action === 'accept') {
        $st = $pdo->prepare("UPDATE friends SET status='accepted' WHERE requester_id=? AND addressee_id=? AND status='pending'");
        $st->execute([$targetId, $me]);
    } elseif ($action === 'remove') {
        $st = $pdo->prepare("DELETE FROM friends WHERE (requester_id=? AND addressee_id=?) OR (requester_id=? AND addressee_id=?)");
        $st->execute([$me, $targetId, $targetId, $me]);
    }
} catch (Throwable $e) {
    // sessiz
}

$return = (string)($_POST['return'] ?? url(''));
header('Location: ' . $return);
exit;