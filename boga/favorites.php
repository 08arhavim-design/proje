<?php
declare(strict_types=1);

require_once __DIR__ . '/app/functions.php';

require_login();

$title = 'Favorilerim';
$error = null;
$favorites = [];

try {
    $pdo = pdo();

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS bull_favorites (
            user_id INT UNSIGNED NOT NULL,
            bull_id INT UNSIGNED NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (user_id, bull_id),
            KEY idx_bull (bull_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    $stmt = $pdo->prepare(
        "SELECT b.id, b.name, b.owner_name, b.city, b.district, b.image, f.created_at
         FROM bull_favorites f
         JOIN bulls b ON b.id = f.bull_id
         WHERE f.user_id = :u AND b.status='approved'
         ORDER BY f.created_at DESC"
    );
    $stmt->execute([':u' => (int)current_user_id()]);
    $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Throwable $e) {
    error_log('favorites.php error: ' . $e->getMessage());
    $error = 'Favoriler alınırken hata oluştu.';
}

$view = __DIR__ . '/views/favorites.php';
include __DIR__ . '/app/layout.php';
